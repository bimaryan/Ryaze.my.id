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
            $endpoint = Setting::getValue('wa_api_endpoint', 'https://api.ryz.my.id/api/whatsapp/v1/send-message');

            if (!$token || !$endpoint) {
                Log::warning('WhatsApp Notification skipped: WA API Token or Endpoint is not configured.');
                return false;
            }

            // Ensure the target number starts with appropriate country code (e.g. 08 -> 628)
            if (str_starts_with($target, '0')) {
                $target = '62' . substr($target, 1);
            }

            // Bearer Token Authorization for the new custom API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->post($endpoint, [
                'to' => $target,
                'message' => $message
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
