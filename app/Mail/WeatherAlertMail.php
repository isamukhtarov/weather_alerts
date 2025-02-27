<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Class WeatherAlertMail
 * @package App\Mail
 */
class WeatherAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private array $weatherData
    ){}

    /**
     * @return $this
     */
    public function build(): self
    {
        return $this
            ->subject('Weather Alert Notification')
            ->markdown('emails.weather_alert', [
                'data' => $this->weatherData
            ]);
    }
}
