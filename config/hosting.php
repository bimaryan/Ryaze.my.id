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
];
