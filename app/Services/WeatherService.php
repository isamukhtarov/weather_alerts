<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\WeatherAlertMail;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

/**
 * Class WeatherService
 * @package App\Services
 */
class WeatherService
{
    const CITIES = ['New York', 'London', 'Moscow', 'Tokyo'];

    private string $apiKey;
    private string $apiUrl;
    private array $weatherData = [];
    private SmsService $smsService;

    /**
     * WeatherService constructor.
     */
    public function __construct()
    {
        $this->apiKey = config('services.weather_api.key');
        $this->apiUrl = config('services.weather_api.url');
        $this->smsService = new SmsService();
    }

    /**
     * @param string $city
     * @return array
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getWeather(string $city): array
    {
        return Http::get("{$this->apiUrl}/current.json", [
            'key' => $this->apiKey,
            'q'   => $city,
        ])->throw()->json();
    }

    /**
     * @param string $city
     * @param int $days
     * @return array
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getForecast(string $city, int $days = 3): array
    {
        return Http::get("{$this->apiUrl}/forecast.json", [
            'key'  => $this->apiKey,
            'q'    => $city,
            'days' => $days,
        ])->throw()->json();
    }

    /**
     * @param string $city
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function checkAndSendAlerts(): void
    {
        $this->setWeatherData();

        if (!empty($this->weatherData)) {
            $this->sendEmailMessage()
                 ->sendMessage();
        }
    }

    /**
     * @return $this
     */
    protected function setWeatherData(): self
    {
        $this->weatherData = collect(self::CITIES)->mapWithKeys(function (string $city) {
            $weather = $this->getForecast($city, 1);
            $forecast = $weather['forecast']['forecastday'][0]['day'];

//            if (!$this->shouldSendAlert($forecast)) {
//                return [];
//            }

            return [$city => [
                'precipitation' => $forecast['totalprecip_mm'],
                'uv_index' => $forecast['uv']
            ]];
        })->toArray();

        return $this;
    }

    /**
     * @param array $forecast
     * @return bool
     */
    protected function shouldSendAlert(array $forecast): bool
    {
        return $forecast['totalprecip_mm'] > 50 || $forecast['uv'] >= 7;
    }

    /**
     * @return $this
     */
    protected function sendEmailMessage(): self
    {
        $emails = User::query()->pluck('email')->unique();

        foreach ($emails as $email) {
            Mail::to($email)->send(new WeatherAlertMail($this->weatherData));
        }

        return $this;
    }

    protected function sendMessage(): void
    {
        $phones = User::query()->pluck('phone')->toArray();

        foreach ($phones as $phone) {
            $this->smsService->sendSms($phone, $this->weatherData);
        }
    }
}
