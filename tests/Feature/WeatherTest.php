<?php

use Illuminate\Support\Facades\Http;
use App\Services\WeatherService;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\WeatherAlertMail;

beforeEach(function () {
    uses(\Illuminate\Foundation\Testing\DatabaseTransactions::class);
});

test('test weather service fetches correct weather data', function () {
    Http::fake([
        '*/current.json*' => Http::response([
            'location' => ['name' => 'London'],
            'current' => [
                'temp_c' => 20,
                'humidity' => 60,
                'uv' => 7,
            ],
        ]),
    ]);

    $service = new WeatherService();
    $weather = $service->getWeather('London');

    $this->assertEquals('London', $weather['location']['name']);
    $this->assertEquals(20, $weather['current']['temp_c']);
    $this->assertEquals(60, $weather['current']['humidity']);
    $this->assertEquals(7, $weather['current']['uv']);
});

test('weather controller returns correct data', function () {
    Http::fake([
        '*/current.json*' => Http::response([
            'location' => ['name' => 'London'],
            'current' => [
                'temp_c' => 20,
                'humidity' => 60,
                'uv' => 7,
            ],
        ]),

        '*/forecast.json*' => Http::response([
            'forecastday' => [
                [
                    'date' => '2025-02-27',
                    'day' => [
                        'maxtemp_c' => 25,
                        'mintemp_c' => 15,
                    ],
                ],
            ],
        ]),
    ]);

    $response = $this->get('/weather?city=London');

    $response->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Weather')
            ->has('weather', fn (AssertableInertia $page) => $page
                ->where('location.name', 'London')
                ->where('current.temp_c', 20)
                ->where('current.humidity', 60)
                ->where('current.uv', 7)
            )->has('forecast', fn (AssertableInertia $page) => $page
                   ->where('forecastday.0.day.maxtemp_c', 25)
                   ->where('forecastday.0.day.mintemp_c', 15)
            )->where('city', 'London')
        );
});

test('test_weather_alert_mail_is_sent', function () {
    Mail::fake();

    $user = User::factory()->create([
        'name' => 'John',
        'email' => 'john@mail.com',
        'phone' => '+111111111'
    ]);

    Http::fake([
        '*/forecast.json*' => Http::response([
            'forecast' => [
                'forecastday' => [
                    [
                        'day' => [
                            'totalprecip_mm' => 60,
                            'uv' => 8,
                        ],
                    ],
                ],
            ]
        ]),
    ]);

    $service = new WeatherService();
    $service->checkAndSendAlerts();

    Mail::assertSent(WeatherAlertMail::class, function ($mail) use ($user) {
        $data = $mail?->getWeatherData();

        return $mail->hasTo($user->email)
            && isset($data)
            && $data['London']['precipitation'] === 60
            && $data['London']['uv_index'] === 8;
    });
});
