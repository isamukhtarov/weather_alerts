<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Http\CurlClient;
use Twilio\Rest\Client;

/**
 * Class SmsService
 * @package App\Services
 */
class SmsService
{
    const INITIAL_CURL_OPTIONS = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];

    private Client $twilio;

    /**
     * SmsService constructor.
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function __construct()
    {
        $http = new CurlClient(self::INITIAL_CURL_OPTIONS);

        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token'),
            null,
            null,
            $http
        );
    }

    /**
     * @param string $to
     * @param array $weatherData
     * @return bool
     */
    public function sendSms(string $to, array $weatherData): bool
    {
        $message = $this->formatWeatherAlert($weatherData);

        try {
            $this->twilio->messages->create($to, [
                'from' => config('services.twilio.from'),
                'body' => $message
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @param array $weatherData
     * @return string
     */
    private function formatWeatherAlert(array $weatherData): string
    {
        $message = "🌦 Weather Alert 🌦\n";
        foreach ($weatherData as $city => $data) {
            $message .= "{$city}:\n🌧️ {$data['precipitation']} mm\n☀ UV: {$data['uv_index']}\n\n";
        }

        return substr($message, 0, 160);
    }
}
