<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * 
     *
     * @var array  
     * 
     */

     protected $commands = [
        \App\Console\Commands\FakeEntry::class,
        \App\Console\Commands\OrderStatus::class,
     ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('order:status')->cron('*/30 * * * *');
        // $schedule->command('fake:entry')->cron('*/10 * * * *');
        $schedule->command('fake:entry')->everyMinute();
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
