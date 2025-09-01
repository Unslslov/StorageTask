<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:fetch-all-data')
            ->twiceDaily(6, 18)
            ->appendOutputTo(storage_path('logs/scheduler.log'))
            ->onSuccess(function () {
                \Log::info('Scheduled data fetch completed successfully');
            })
            ->onFailure(function () {
                \Log::error('Scheduled data fetch failed');
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
