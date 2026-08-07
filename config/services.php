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
        'text_model' => env('GROQ_TEXT_MODEL', 'llama-3.3-70b-versatile'),
    ],

];

