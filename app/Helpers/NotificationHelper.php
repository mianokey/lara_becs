<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * Send WhatsApp message via external API
     *
     * @param string $number Full phone number with country code
     * @param string $message Message text
     * @return array|null API response
     */
    public static function sendWhatsApp(string $number, string $message): ?array
    {
        try {
            $url = 'https://wa.nux.my.id/api/sendWA';
            $secret = config('services.whatsapp.secret'); // we'll store secret in config

            $response = Http::get($url, [
                'to' => $number,
                'msg' => $message,
                'secret' => $secret,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed: " . $e->getMessage());
            return null;
        }
    }
}
