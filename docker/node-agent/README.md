# Ryaze Node Runtime Container (`ryaze-node`)

Runtime Node terpisah untuk aplikasi framework `node` klien. Proses Node user
**tidak pernah** di-spawn dari PHP — panel Laravel hanya mengirim perintah ke
agent HTTP di dalam container ini, dan nginx (OpenResty) merutekan traffic
publik berdasarkan file marker `.port` + `.proxy-host` yang ditulis agent.

```
[Panel PHP/Laravel] --HTTP+token--> [ryaze-node: agent + PM2 -> aplikasi user]
        |
        +-- nginx/OpenResty membaca .port & .proxy-host --> proxy ke container ini
```

## Setup sekali di server

1. **Siapkan token rahasia** dan tulis di `.env` panel (Laravel):

   ```
   NODE_RUNTIME_URL=http://ryaze-node:4700
   NODE_RUNTIME_TOKEN=<token-rahasia-acak>
   ```

2. **Kenali network Docker** yang dipakai container PHP:

   ```bash
   docker inspect <nama-container-php> --format '{{range $k,$v := .NetworkSettings.Networks}}{{$k}} {{end}}'
   ```

3. **Build & jalankan** (dari root repo di server):

   ```bash
   export NODE_RUNTIME_TOKEN=<token-rahasia-acak>
   docker compose -f docker/docker-compose.node.yml up -d --build
   ```

   Edit bagian `networks:` di `docker/docker-compose.node.yml` agar sesuai
   nama network di server Anda (contoh di file memakai `ryaze-net`).

4. **Pastikan volume sama** dengan yang dipakai nginx/panel untuk folder klien
   (`/www/sites/hosting_clients`). Kalau di server Anda path-nya beda, ubah
   bagian `volumes:` dan `CLIENTS_DIR`.

5. **Cek agent hidup:**

   ```bash
   curl http://127.0.0.1:4700/health
   # {"ok":true,"version":"1.0.0","pm2":true,"clients_dir":"/www/sites/hosting_clients"}
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
