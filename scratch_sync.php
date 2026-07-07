<?php

use App\Models\HostingEmail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

$emails = HostingEmail::all();
$url = env('POSTE_IO_URL');
$user = env('POSTE_IO_USER');
$pass = env('POSTE_IO_PASSWORD');

if (!$url || !$user || !$pass) {
    echo "Missing credentials\n";
    exit;
}

$apiUrl = rtrim($url, '/') . '/admin/api/v1';

foreach ($emails as $email) {
    echo "Syncing: {$email->email_address}...\n";
    try {
        $rawPassword = Crypt::decryptString($email->password);
        
        // 1. Create domain if not exists
        $resDomain = Http::withoutVerifying()
            ->withBasicAuth($user, $pass)
            ->post("{$apiUrl}/domains", [
                'name' => $email->domain,
            ]);
            
        // 2. Create box
        $resBox = Http::withoutVerifying()
            ->withBasicAuth($user, $pass)
            ->post("{$apiUrl}/boxes", [
                'name' => explode('@', $email->email_address)[0],
                'email' => $email->email_address,
                'passwordPlaintext' => $rawPassword,
            ]);
            
        if ($resBox->successful()) {
            echo "✅ Success: {$email->email_address}\n";
        } else {
            echo "❌ Failed: {$email->email_address} - " . $resBox->body() . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Exception for {$email->email_address}: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
