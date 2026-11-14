<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pakasir Payment Gateway
    |--------------------------------------------------------------------------
    */
    'pakasir' => [
        'slug' => env('PAKASIR_SLUG', 'ryaze'),
        'api_key' => env('PAKASIR_API_KEY'),
        // Secret untuk verifikasi header X-Secret pada webhook v2.
        // Ambil/reset di halaman detail proyek di dashboard Pakasir.
        'webhook_secret' => env('PAKASIR_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | DOKU Payment Gateway (DOKU Checkout, non-SNAP API)
    |--------------------------------------------------------------------------
    | Kredensial diambil dari DOKU Back Office.
    | Base URL sandbox: https://api-sandbox.doku.com
    | Base URL produksi: https://api.doku.com
    */
    'doku' => [
        'client_id' => env('DOKU_CLIENT_ID'),
        'secret_key' => env('DOKU_SECRET_KEY'),
        'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com'),
        // Opsional: batasi metode pembayaran di halaman checkout (koma sebagai pemisah).
        // Contoh: DOKU_PAYMENT_METHODS=QRIS,CREDIT_CARD,EMONEY_DANA
        // Kosong/null = tampilkan semua kanal yang aktif di akun DOKU.
        'payment_methods' => env('DOKU_PAYMENT_METHODS')
            ? array_map('trim', explode(',', env('DOKU_PAYMENT_METHODS')))
            : null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudflare DNS Management
    |--------------------------------------------------------------------------
    */

    'cloudflare' => [
        'zone_id' => env('CLOUDFLARE_ZONE_ID'),
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'tunnel_url' => env('CLOUDFLARE_TUNNEL_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Tunnel (Zero Trust) — Published Application Routes
    |--------------------------------------------------------------------------
    | Untuk mendaftarkan ingress rule pada tunnel yang remotely-managed
    | (dikelola lewat dashboard). Token WAJIB punya scope Zero Trust/Tunnel
    | (akun-level), BERBEDA dari CLOUDFLARE_API_TOKEN yang hanya zone-scoped.
    | Tanpa ini, route subdomain tidak terdaftar otomatis & web jadi 502.
    */

    'cloudflare_tunnel' => [
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
        'tunnel_id' => env('CLOUDFLARE_TUNNEL_ID'),
        'api_token' => env('CLOUDFLARE_TUNNEL_API_TOKEN'),
        // URL origin yang direach cloudflared (biasanya nginx/OpenResty di host).
        // Default: nginx container mempublish port 80 di host.
        'service' => env('CLOUDFLARE_TUNNEL_SERVICE', 'http://localhost:80'),
    ],


    /*
    |--------------------------------------------------------------------------
    | Panel MySQL (untuk provisioning database klien)
    |--------------------------------------------------------------------------
    */
    'panel_mysql' => [
        'root_password' => env('PANEL_MYSQL_ROOT_PASSWORD'),
        'host' => env('PANEL_MYSQL_HOST', '1Panel-mysql-KZAi'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Panel PostgreSQL (untuk provisioning database pgsql klien)
    |--------------------------------------------------------------------------
    */
    'panel_pgsql' => [
        'host' => env('PANEL_PGSQL_HOST'),
        'port' => env('PANEL_PGSQL_PORT', '5432'),
        'user' => env('PANEL_PGSQL_USER'),
        'password' => env('PANEL_PGSQL_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Poste.io (email hosting klien)
    |--------------------------------------------------------------------------
    */
    'poste' => [
        'url' => env('POSTE_IO_URL'),
        'user' => env('POSTE_IO_USER'),
        'password' => env('POSTE_IO_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Android build (Apk Builder)
    |--------------------------------------------------------------------------
    */
    'apk_build' => [
        'java_home' => env('JAVA_HOME', '/usr/lib/jvm/java-17-openjdk'),
        'android_sdk_root' => env('ANDROID_SDK_ROOT', '/opt/android-sdk'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API eksternal
    |--------------------------------------------------------------------------
    */
    'api' => [
        'cors_allowed_origins' => env('API_CORS_ALLOWED_ORIGINS', 'https://ryaze.my.id'),
        'debug' => env('APP_DEBUG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reverb WebSocket (shared key untuk tunnel client)
    |--------------------------------------------------------------------------
    */
    'reverb' => [
        'app_key' => env('REVERB_APP_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | phpMyAdmin
    |--------------------------------------------------------------------------
    */
    'pma' => [
        'url' => env('PMA_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    */
    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI - generator artikel dan gambar sampul
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'text_model' => env('OPENAI_TEXT_MODEL', 'gpt-5.6'),
        'image_model' => env('OPENAI_IMAGE_MODEL', 'gpt-image-2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Groq - generator artikel
    |--------------------------------------------------------------------------
    */
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'text_model' => env('GROQ_TEXT_MODEL', 'openai/gpt-oss-120b'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth (Socialite)
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Hosting Container Names
    |--------------------------------------------------------------------------
    | Untuk networking cross-container (nginx -> PHP container).
    | IP container PHP digunakan sebagai proxy target di nginx.
    */
    'php_container' => env('PHP_CONTAINER_NAME', '1Panel-php8-aJQI'),

    /*
    |--------------------------------------------------------------------------
    | Node Runtime Container (ryaze-node)
    |--------------------------------------------------------------------------
    | Runtime terpisah untuk aplikasi framework 'node' milik klien. Panel PHP
    | tidak pernah men-spawn proses Node — semuanya lewat agent HTTP di
    | container ini. Lihat docker/node-agent/README.md untuk setup.
    */
    'node_runtime' => [
        // URL agent, mis. http://ryaze-node:4700 (docker network) atau
        // http://127.0.0.1:4700 (bind localhost). Kosong = fitur aplikasi Node nonaktif.
        'url' => env('NODE_RUNTIME_URL'),
        // Token shared secret (header X-Agent-Token), sama dengan AGENT_TOKEN di container.
        'token' => env('NODE_RUNTIME_TOKEN'),
        // Timeout perintah build (detik) — npm install bisa lama.
        'build_timeout' => env('NODE_RUNTIME_BUILD_TIMEOUT', 900),
    ],

];

