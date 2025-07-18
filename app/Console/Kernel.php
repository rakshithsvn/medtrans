<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CronJob::class,
        Commands\CronJob1::class,
        Commands\CronJob2::class,
	Commands\CronJob3::class,
	Commands\CronJobOrder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('CronJob:cronjob')->everyTenMinutes();
	$schedule->command('CronJob3:cronjob')->everyTenMinutes();
	//$schedule->command('CronJob1:cronjob')->everyFifteenMinutes();
	$schedule->command('CronJobOrder:cronjob')->hourly();
	// $schedule->command('CronJob2:cronjob')->hourly();

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
