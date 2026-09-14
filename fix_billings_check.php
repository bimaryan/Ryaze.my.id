<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HostingBilling;
use Carbon\Carbon;

echo "=== Billing paid plans dengan next_due_date > 5 tahun ke depan ===\n";
$wrong = HostingBilling::where('status', 'active')
    ->where('plan', '!=', 'free')
    ->where('next_due_date', '>', now()->addYears(5))
    ->get();

echo "Jumlah: " . $wrong->count() . "\n";
foreach ($wrong as $b) {
    echo "ID: {$b->id} | User: {$b->user_id} | Plan: {$b->plan} | Due: {$b->next_due_date}\n";
}
