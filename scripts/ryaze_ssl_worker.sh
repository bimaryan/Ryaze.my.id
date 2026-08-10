#!/bin/bash

# Pastikan dijalankan sebagai root
if [ "$EUID" -ne 0 ]; then
  echo "Harap jalankan script ini sebagai root (sudo)"
  exit 1
fi

APP_DIR="/opt/1panel/apps/openresty/openresty/www/sites/ryaze.my.id/index"
QUEUE_FILE="$APP_DIR/storage/app/ssl_queue.json"
NGINX_QUEUE="$APP_DIR/storage/app/nginx_queue.json"
NGINX_CONF_DIR="/opt/1panel/apps/openresty/openresty/conf/conf.d"
NGINX_SSL_DIR="/opt/1panel/apps/openresty/openresty/www/ssl"
CERTBOT_WEBROOT="/opt/1panel/apps/openresty/openresty/www/letsencrypt"
CONTAINER_PHP="1Panel-php8-aJQI"
CONTAINER_NGINX="1Panel-openresty-iLJL"

# Tulis konfigurasi default vhost (dipakai saat reset konfigurasi custom)
# Template meniru hosting_clients.conf: PHP-FPM 127.0.0.1:9000, static root,
# dan Node.js via file .port — jadi aman untuk subdomain ryaze/ryz/safetalkai.
write_default_conf() {
    local DOMAIN="$1" PROJECT_DOMAIN="$2"
    local SUBDOMAIN="${DOMAIN%%.*}"
    local CONF_FILE="$NGINX_CONF_DIR/$DOMAIN.conf"
    local CLIENT_DIR="/www/sites/hosting_clients/$SUBDOMAIN"
    mkdir -p "$NGINX_CONF_DIR"
    cat <<EOF > "$CONF_FILE"
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;

    add_header X-Frame-Options "" always;

    set \$dynamic_root $CLIENT_DIR;
    if (-f $CLIENT_DIR/public/index.php) {
        set \$dynamic_root $CLIENT_DIR/public;
    }
    if (-f $CLIENT_DIR/public/index.html) {
        set \$dynamic_root $CLIENT_DIR/public;
    }
    if (-f $CLIENT_DIR/dist/index.html) {
        set \$dynamic_root $CLIENT_DIR/dist;
    }
    if (-f $CLIENT_DIR/build/index.html) {
        set \$dynamic_root $CLIENT_DIR/build;
    }

    root \$dynamic_root;
    index index.php index.html index.htm;

    access_log /www/sites/hosting_clients/log/access.log main;
    error_log /www/sites/hosting_clients/log/error.log;

    location ^~ /.well-known/acme-challenge {
        allow all;
        root /usr/share/nginx/html;
    }

    set_by_lua_block \$app_port {
        local subdomain = "$SUBDOMAIN"
        local file_path = "/www/sites/hosting_clients/" .. subdomain .. "/.port"
        local file = io.open(file_path, "r")
        if file then
            local port = file:read("*l")
            file:close()
            if port then
                port = port:gsub("%s+", "")
                if port ~= "" then
                    return port
                end
            end
        end
        return ""
    }

    proxy_http_version 1.1;
    proxy_set_header Upgrade \$http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host \$host;
    proxy_set_header X-Real-IP \$remote_addr;
    proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto \$scheme;

    error_page 418 = @app_proxy;

    location / {
        if (\$app_port != "") {
            return 418;
        }
        try_files \$uri \$uri/ @framework_fallback;
    }

    location @framework_fallback {
        if (-f \$document_root/index.php) {
            rewrite ^ /index.php?\$query_string last;
        }
        if (-f \$document_root/index.html) {
            rewrite ^ /index.html last;
        }
        return 404;
    }

    location ~ \.php\$ {
        if (\$app_port != "") {
            return 418;
        }
        try_files \$uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param HTTP_HOST \$host;
    }

    location ~ .*\.(js|css|png|jpg|jpeg|gif|ico|bmp|swf|eot|svg|ttf|woff|woff2)\$ {
        if (\$app_port != "") {
            return 418;
        }
        try_files \$uri \$uri/ @framework_fallback;
        expires 30d;
        log_not_found off;
    }

    location @app_proxy {
        proxy_pass http://127.0.0.1:\$app_port;
    }
}
EOF
    chown root:root "$CONF_FILE"
}

# Keluar hanya bila KEDUA antrean (SSL & Nginx custom) tidak ada
if [ ! -f "$QUEUE_FILE" ] && [ ! -f "$NGINX_QUEUE" ]; then
    exit 0
fi

# Pastikan jq terinstal (dipakai kedua antrean)
if ! command -v jq &> /dev/null; then
    echo "jq tidak terinstal. Menginstal jq..."
    apt-get update && apt-get install -y jq
fi

# ──────────────────────────────────────────────────────────────────
# Antrean Konfigurasi Nginx Custom per Subdomain (nginx_queue.json)
# Dijalankan lebih dulu agar independen dari antrean SSL.
# ──────────────────────────────────────────────────────────────────
if [ -f "$NGINX_QUEUE" ]; then
    NQUEUE_CONTENT=$(cat "$NGINX_QUEUE")

    if [ -n "$NQUEUE_CONTENT" ] && [ "$NQUEUE_CONTENT" != "[]" ]; then
        # Kosongkan antrean agar tidak ada duplikasi
        echo "[]" > "$NGINX_QUEUE"

        NLEN=$(echo "$NQUEUE_CONTENT" | jq '. | length')

        for i in $(seq 0 $(($NLEN - 1))); do
            ACTION=$(echo "$NQUEUE_CONTENT" | jq -r ".[$i].action")
            DOMAIN=$(echo "$NQUEUE_CONTENT" | jq -r ".[$i].domain")
            PROJECT_DOMAIN=$(echo "$NQUEUE_CONTENT" | jq -r ".[$i].project_domain")
            CUSTOM_FILE=$(echo "$NQUEUE_CONTENT" | jq -r ".[$i].custom_file")

            CONF_FILE="$NGINX_CONF_DIR/$DOMAIN.conf"
            NGINX_ERR_DIR="$APP_DIR/storage/app/nginx/errors"

            echo "[$(date)] Memproses task: $ACTION untuk domain $DOMAIN" >> "$APP_DIR/storage/logs/ssl_worker.log"

            if [ "$ACTION" == "custom" ]; then
                mkdir -p "$NGINX_ERR_DIR"

                if [ -z "$CUSTOM_FILE" ] || [ ! -f "$APP_DIR/$CUSTOM_FILE" ]; then
                    # Reset ke konfigurasi default (HTTPS bila sertifikat tersedia)
                    rm -f "$NGINX_ERR_DIR/$DOMAIN.txt"
                    write_default_conf "$DOMAIN" "$PROJECT_DOMAIN"
                    docker exec $CONTAINER_NGINX nginx -s reload
                    docker exec $CONTAINER_PHP php /www/sites/ryaze.my.id/index/artisan domain:nginx-status "$DOMAIN" reset
                else
                    # Backup config lama, lalu pasang config baru user
                    cp -f "$CONF_FILE" "$CONF_FILE.bak" 2>/dev/null
                    cp -f "$APP_DIR/$CUSTOM_FILE" "$CONF_FILE"
                    chown root:root "$CONF_FILE"

                    NGINX_TEST=$(docker exec $CONTAINER_NGINX nginx -t 2>&1)
                    if [ $? -eq 0 ]; then
                        # Config valid — reload & laporkan sukses
                        rm -f "$NGINX_ERR_DIR/$DOMAIN.txt"
                        docker exec $CONTAINER_NGINX nginx -s reload
                        docker exec $CONTAINER_PHP php /www/sites/ryaze.my.id/index/artisan domain:nginx-status "$DOMAIN" applied
                    else
                        # Config invalid — rollback (ke backup bila ada, selain itu default)
                        if [ -f "$CONF_FILE.bak" ]; then
                            cp -f "$CONF_FILE.bak" "$CONF_FILE" 2>/dev/null
                        else
                            write_default_conf "$DOMAIN" "$PROJECT_DOMAIN"
                        fi
                        echo "$NGINX_TEST" > "$NGINX_ERR_DIR/$DOMAIN.txt"
                        docker exec $CONTAINER_PHP php /www/sites/ryaze.my.id/index/artisan domain:nginx-status "$DOMAIN" failed
                    fi
                fi
            fi
        done
    fi
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
    
    LOG_FILE="$APP_DIR/storage/logs/ssl_worker.log"
    echo "[$(date)] Memproses task: $ACTION untuk domain $DOMAIN" >> "$LOG_FILE"
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
        # Pastikan folder webroot dan conf ada sebelum menjalankan certbot
        mkdir -p "$CERTBOT_WEBROOT"
        mkdir -p "$NGINX_CONF_DIR"
        
        # Regenerate HTTP block agar Nginx siap menerima request Let's Encrypt
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
        sleep 2
        
        # Jalankan Certbot
        OUTPUT=$(certbot certonly --webroot -w "$CERTBOT_WEBROOT" -d "$DOMAIN" --non-interactive --agree-tos -m admin@ryaze.my.id 2>&1)
        
        if echo "$OUTPUT" | grep -E "Congratulations|Successfully|Certificate not yet due for renewal"; then
            # SSL Sukses
            echo "[$(date)] Certbot BERHASIL untuk $DOMAIN. Output: $OUTPUT" >> "$LOG_FILE"
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
        set \$do_redirect 0;
        if (\$http_x_forwarded_proto != "https") {
            set \$do_redirect 1;
        }
        if (\$do_redirect = 1) {
            return 301 https://\$host\$request_uri;
        }

        proxy_pass http://127.0.0.1;
        
        proxy_set_header Host $PROJECT_DOMAIN;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
        proxy_set_header X-Forwarded-Host \$host;
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
        proxy_set_header X-Forwarded-Proto https;
        proxy_set_header X-Forwarded-Host \$host;
    }
}
EOF
            docker exec $CONTAINER_NGINX nginx -s reload
            
            # Panggil Artisan Command di dalam container PHP untuk update status
            docker exec $CONTAINER_PHP php /www/sites/ryaze.my.id/index/artisan domain:ssl-status "$DOMAIN" active
        else
            # SSL Gagal
            echo "[$(date)] Certbot GAGAL untuk $DOMAIN. Output: $OUTPUT" >> "$LOG_FILE"
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
