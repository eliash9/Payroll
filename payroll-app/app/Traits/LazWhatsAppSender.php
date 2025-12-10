<?php

namespace App\Traits;

use App\Models\LazSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait LazWhatsAppSender
{
    /**
     * Send WhatsApp message using configured API.
     *
     * @param string $phone
     * @param string $message
     * @return void
     * @throws \Exception
     */
    public function sendWhatsAppMessage(string $phone, string $message)
    {
        $url = LazSetting::where('key', 'whatsapp_api_url')->value('value');
        $apiKey = LazSetting::where('key', 'whatsapp_api_key')->value('value');

        if (!$url || !$apiKey) {
            // Log::warning('WhatsApp configurations missing. Message not sent.');
            return; // Fail silently or throw, depends on preference. I'll return but controller might want to know.
            // Actually for 'Test' we want to throw. For background process we might want log.
            // But this method is 'send', implies action.
            // Let's check call site.
            // I'll throw if missing configuration ONLY if it's explicitly called for testing?
            // No, simpler to just throw and let caller catch.
             throw new \Exception('Konfigurasi WhatsApp belum lengkap.');
        }

        // Format phone: 08xx -> 628xx
        // Remove non-digits
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            // Flexible payload. Most providers support 'to' or 'number' or 'phone'.
            // I will try to support generic structure.
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'to' => $phone,
                'number' => $phone,
                'phone' => $phone, // Redundant but safe for different providers
                'message' => $message,
                'text' => $message, // Some use text
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp API Error: ' . $response->body());
                throw new \Exception('WhatsApp API Error: ' . $response->status() . ' ' . $response->body());
            }
            
            Log::info("WhatsApp sent to $phone");

        } catch (\Exception $e) {
            Log::error('WhatsApp Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
