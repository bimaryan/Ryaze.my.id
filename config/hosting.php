<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hosting Clients Directory
    |--------------------------------------------------------------------------
    |
    | Direktori penyimpanan project hosting user. Semua kode harus memakai
    | helper hosting_clients_dir() (app/helpers.php) yang membaca nilai ini,
    | bukan path hardcoded di masing-masing controller/job.
    |
    */
    'client_dir' => env('HOSTING_CLIENTS_DIR', '/www/sites/hosting_clients'),

    /*
    |--------------------------------------------------------------------------
    | Basis Domain Platform
    |--------------------------------------------------------------------------
    |
    | Dipakai untuk MENGENALI hostname subdomain platform yang sudah ada
    | sebelum fitur subdomain diperluas, mis. "silk.ryz.my.id". Data itu tidak
    | punya zone sendiri sehingga resolution-nya diambil dari domain utama.
    |
    | User TIDAK bisa membuat subdomain baru di basis ini — subdomain hanya
    | boleh dibuat dari custom domain milik user sendiri.
    |
    | WAJIB sinkron dengan regex `server_name` di
    | conf.d/hosting_clients.conf:
    |   ~^(?<regex_subdomain>[a-z0-9-]+)\.(ryaze|ryz|safetalkai)\.my\.id$
    |
    */
    'public_bases' => [
        'ryaze.my.id',
        'ryz.my.id',
        'safetalkai.my.id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Batas Hostname per Project
    |--------------------------------------------------------------------------
    |
    | Satu project boleh dilayani banyak hostname (custom domain + subdomain),
    | tapi dibatasi supaya user tidak bisa attach puluhan hostname ke satu
    | project dan membebani DNS. Isi null untuk tanpa batas.
    |
    */
    'hostnames_per_project' => 25,
];
