<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message.
     * 
     * @param string $target Target phone number
     * @param string $message The message body
     * @return array|bool
     */
    public static function send($target, $message)
    {
        try {
            $token = Setting::getValue('wa_api_token');
            $endpoint = Setting::getValue('wa_api_endpoint', 'https://api.fonnte.com/send'); // default Fonnte

            if (!$token || !$endpoint) {
                Log::warning('WhatsApp Notification skipped: WA API Token or Endpoint is not configured.');
                return false;
            }

            // Ensure the target number starts with appropriate country code (e.g. 08 -> 628)
            if (str_starts_with($target, '0')) {
                $target = '62' . substr($target, 1);
            }

            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post($endpoint, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Optional for Fonnte
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('WhatsApp API Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return false;
        }
    }
}
