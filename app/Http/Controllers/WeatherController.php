<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Class WeatherController
 * @package App\Http\Controllers
 */
class WeatherController extends Controller
{
    const DEFAULT_CITY = 'Moscow';

    /**
     * WeatherController constructor.
     * @param WeatherService $service
     */
    public function __construct(
        private WeatherService $service
    )
    {}

    /**
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function __invoke(Request $request): Response
    {
        $city = $request->query('city', self::DEFAULT_CITY);
        // dd($this->service->getWeather($city), $this->service->getForecast($city), $city);
        return Inertia::render('Weather', [
            'weather' => $this->service->getWeather($city),
            'forecast' => $this->service->getForecast($city),
            'city' => $city
        ]);
    }
}
