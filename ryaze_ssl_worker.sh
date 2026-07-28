#!/bin/bash

# Pastikan dijalankan sebagai root
if [ "$EUID" -ne 0 ]; then
  echo "Harap jalankan script ini sebagai root (sudo)"
  exit 1
fi

APP_DIR="/opt/1panel/apps/openresty/openresty/www/sites/ryaze.my.id/index"
QUEUE_FILE="$APP_DIR/storage/app/ssl_queue.json"
NGINX_CONF_DIR="/opt/1panel/apps/openresty/openresty/conf/conf.d/custom_domains"
NGINX_SSL_DIR="/opt/1panel/apps/openresty/openresty/www/ssl"
CERTBOT_WEBROOT="/opt/1panel/apps/openresty/openresty/www/letsencrypt"
CONTAINER_PHP="1Panel-php8-aJQI"
CONTAINER_NGINX="1panel-openresty"

if [ ! -f "$QUEUE_FILE" ]; then
    exit 0
fi

# Pastikan jq terinstal
if ! command -v jq &> /dev/null; then
    echo "jq tidak terinstal. Menginstal jq..."
    apt-get update && apt-get install -y jq
fi

# Baca isi antrean
QUEUE_CONTENT=$(cat "$QUEUE_FILE")

# Jika kosong atau bukan array valid
if [ -z "$QUEUE_CONTENT" ] || [ "$QUEUE_CONTENT" == "[]" ]; then
    exit 0
fi

# Bersihkan file queue agar tidak ada duplikasi request
echo "[]" > "$QUEUE_FILE"

# Parse array
LENGTH=$(echo "$QUEUE_CONTENT" | jq '. | length')

for i in $(seq 0 $(($LENGTH - 1))); do
    ACTION=$(echo "$QUEUE_CONTENT" | jq -r ".[$i].action")
    DOMAIN=$(echo "$QUEUE_CONTENT" | jq -r ".[$i].domain")
    PROJECT_DOMAIN=$(echo "$QUEUE_CONTENT" | jq -r ".[$i].project_domain")
    
    echo "[$(date)] Memproses task: $ACTION untuk domain $DOMAIN"
    
    CONF_FILE="$NGINX_CONF_DIR/$DOMAIN.conf"
    
    if [ "$ACTION" == "add" ]; then
        # Buat konfigurasi Nginx HTTP saja
        mkdir -p "$NGINX_CONF_DIR"
        mkdir -p "$CERTBOT_WEBROOT"
        
        cat <<EOF > "$CONF_FILE"
server {
    listen 80;
    server_name $DOMAIN;

    location /.well-known/acme-challenge/ {
        root /www/letsencrypt;
    }

    location / {
        proxy_pass http://127.0.0.1;
        proxy_set_header Host $PROJECT_DOMAIN;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF
        chown root:root "$CONF_FILE"
        docker exec $CONTAINER_NGINX nginx -s reload
        
    elif [ "$ACTION" == "ssl" ]; then
        # Jalankan Certbot
        OUTPUT=$(certbot certonly --webroot -w "$CERTBOT_WEBROOT" -d "$DOMAIN" --non-interactive --agree-tos -m admin@ryaze.my.id 2>&1)
        
        if echo "$OUTPUT" | grep -E "Congratulations|Successfully|Certificate not yet due for renewal"; then
            # SSL Sukses
            mkdir -p "$NGINX_SSL_DIR/$DOMAIN"
            cp "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" "$NGINX_SSL_DIR/$DOMAIN/fullchain.pem"
            cp "/etc/letsencrypt/live/$DOMAIN/privkey.pem" "$NGINX_SSL_DIR/$DOMAIN/privkey.pem"
            
            # Update Config HTTPS
            cat <<EOF > "$CONF_FILE"
server {
    listen 80;
    server_name $DOMAIN;

    location /.well-known/acme-challenge/ {
        root /www/letsencrypt;
    }

    location / {
        return 301 https://\$host\$request_uri;
    }
}

server {
    listen 443 ssl http2;
    server_name $DOMAIN;

    ssl_certificate /www/ssl/$DOMAIN/fullchain.pem;
    ssl_certificate_key /www/ssl/$DOMAIN/privkey.pem;

    location / {
        proxy_pass http://127.0.0.1;
        proxy_set_header Host $PROJECT_DOMAIN;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF
            docker exec $CONTAINER_NGINX nginx -s reload
            
            # Panggil Artisan Command di dalam container PHP untuk update status
            docker exec $CONTAINER_PHP php /www/sites/ryaze.my.id/index/artisan domain:ssl-status "$DOMAIN" active
        else
            # SSL Gagal
            echo "[$(date)] Certbot gagal untuk $DOMAIN. Output: $OUTPUT"
            docker exec $CONTAINER_PHP php /www/sites/ryaze.my.id/index/artisan domain:ssl-status "$DOMAIN" failed
        fi
        
    elif [ "$ACTION" == "delete" ]; then
        rm -f "$CONF_FILE"
        rm -rf "$NGINX_SSL_DIR/$DOMAIN"
        certbot delete --cert-name "$DOMAIN" --non-interactive 2>/dev/null
        docker exec $CONTAINER_NGINX nginx -s reload
    fi
done
