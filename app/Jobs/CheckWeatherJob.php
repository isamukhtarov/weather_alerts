<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\WeatherService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CheckWeatherJob
 * @package App\Jobs
 */
class CheckWeatherJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * @param WeatherService $service
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(WeatherService $service): void
    {
        $service->checkAndSendAlerts();
    }
}
