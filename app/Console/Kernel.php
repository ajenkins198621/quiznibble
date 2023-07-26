<?php

namespace App\Console;

use App\Models\UserStreak;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Set user's streak to 0 if they haven't taken a quiz today
        $schedule->call(function () {
            UserStreak::where('last_quiz_date', '<', now()->startOfDay())
                       ->update(['streak' => 0]);
        })
            ->timezone('America/Denver')
            ->dailyAt('17:59');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
