<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payments = App\Models\HostingPayment::where('status', 'paid')->get();
foreach ($payments as $p) {
    echo "ID: " . $p->id . " | Amount: " . $p->amount . " | Method: " . $p->payment_method . " | Notes: " . $p->notes . "\n";
}
