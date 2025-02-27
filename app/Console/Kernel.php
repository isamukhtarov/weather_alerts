<?php

declare(strict_types=1);

namespace App\Console;

use App\Jobs\CheckWeatherJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(CheckWeatherJob::class)->dailyAt('08:00');
    }
}
