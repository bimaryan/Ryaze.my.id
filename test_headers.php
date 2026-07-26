<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = response('body', 200);
$response->header('content-type', 'text/css');
echo "Content-Type: " . $response->headers->get('content-type') . "\n";

$response2 = response('body', 200);
$response2->header('Content-Type', 'application/javascript');
echo "Content-Type: " . $response2->headers->get('content-type') . "\n";
