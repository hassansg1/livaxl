<?php

namespace FleetCart\Console;

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
        Commands\ScaffoldModuleCommand::class,
        Commands\ScaffoldEntityCommand::class,
        Commands\DownloadProductXML::class,
        Commands\DownloadProductAttributeXML::class,
        Commands\SyncProduct::class,
        Commands\UpdateTempAttributes::class,
        Commands\UpdateTempProducts::class
    ];


    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
    //   $schedule->command("productattributesxml:download")->everySixHours();
    //   $schedule->command("productxml:download")->everySixHours();
    //   $schedule->command("tempproducts:update")->everyTwoHours();
      $schedule->command("product:sync")->everyTwoHours();
        // $schedule->command('inspire')
        //          ->hourly();
    }
}
