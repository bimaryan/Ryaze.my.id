# Ryaze Node Runtime Container (`ryaze-node`)

Runtime Node terpisah untuk aplikasi framework `node` klien. Proses Node user
**tidak pernah** di-spawn dari PHP — panel Laravel hanya mengirim perintah ke
agent HTTP di dalam container ini, dan nginx (OpenResty) merutekan traffic
publik berdasarkan file marker `.port` + `.proxy-host` yang ditulis agent.
**Agent TIDAK dijalankan manual dengan `node agent.js` di server** — selalu
lewat docker compose di bawah.

```
[Panel PHP/Laravel] --HTTP+token--> [ryaze-node: agent + PM2 -> aplikasi user]
        |
        +-- nginx/OpenResty membaca .port & .proxy-host --> proxy ke container ini
```

## Setup sekali di server

> **Jangan jalankan `node agent.js` manual di server** — agent hidup di dalam
> container `ryaze-node` lewat docker compose. Yang manual itu hanya mode dev
> di Windows.

1. **Pull kode terbaru & migrate:**

   ```bash
   cd /opt/.../lokasi-repo-laravel    # sesuaikan dengan lokasi repo di server
   git pull
   docker exec -u root 1Panel-php8-aJQI sh -c 'cd /www/ryaze && php artisan migrate --force && php artisan config:clear'
   ```

2. **Cek network & mount folder klien** yang dipakai container PHP + openresty:

   ```bash
   # Network docker yang dipakai PHP container:
   docker inspect 1Panel-php8-aJQI --format '{{range $k,$v := .NetworkSettings.Networks}}{{$k}} {{end}}'

   # Folder hosting_clients di HOST (sumber volume openresty):
   docker inspect <openresty-container> --format '{{range .Mounts}}{{.Source}} -> {{.Destination}}{{"\n"}}{{end}}' | grep -i hosting
   ```

3. **Build & jalankan container** (dari root repo di server):

   ```bash
   # Nilai wajib (simpan token-nya, dipakai lagi di .env panel):
   export NODE_RUNTIME_TOKEN=$(openssl rand -hex 32)
   export DOCKER_NETWORK=1panel-network                # nama network dari langkah 2
   export HOST_CLIENTS_DIR=/opt/1panel/apps/openresty/openresty/www/sites/hosting_clients

   docker compose -f docker/docker-compose.node.yml up -d --build
   ```

4. **Isi `.env` panel (Laravel)** dengan nilai yang sama:

   ```
   NODE_RUNTIME_URL=http://ryaze-node:4700
   NODE_RUNTIME_TOKEN=<token-dari-langkah-3>
   ```

   Lalu `php artisan config:clear` (+ restart queue worker jika berjalan).

5. **Cek agent hidup:**

   ```bash
   curl http://127.0.0.1:4700/health
   # {"ok":true,"version":"1.0.0","pm2":true,"clients_dir":"/www/sites/hosting_clients"}
   ```

6. **Pastikan volume sama** dengan yang dilihat nginx/panel: agent menulis
   `.port` + `.proxy-host` ke folder yang juga di-mount openresty sebagai
   `/www/sites/hosting_clients` (cek langkah 2). Panel membaca log langsung
   dari folder yang sama.

7. **Migrasi project node lama** (yang masih manual `ryaze-start`):

   ```bash
   docker exec -u root 1Panel-php8-aJQI sh -c 'cd /www/ryaze && php artisan apps:migrate-legacy-node --start'
   ```

## File marker per project (volume bersama)

| File | Ditulis oleh | Dibaca oleh |
|---|---|---|
| `.port` | agent (saat start) | nginx Lua → `proxy_pass` |
| `.proxy-host` | agent (IP container node) | nginx Lua → `proxy_pass` |
| `.app.log` | agent (stdout/stderr app) | panel (tab Logs) |
| `.app.json` | agent (param start terakhir) | agent `/restart` |
| `.build.log` | agent (hasil build) | panel |

## Catatan

- Agent menolak semua perintah tanpa token (`X-Agent-Token`) dan menolak path
  di luar `CLIENTS_DIR`.
- Port aplikasi dialokasi acak dari range `3000-3400` dan `8000-8100` (bisa
  diubah via env `AGENT_PORT_RANGES`).
- Mode proses: PM2 (autorestart + limit memori 512M) bila tersedia, fallback
  detached spawn bila tidak.
