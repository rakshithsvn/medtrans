<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CronJobOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronJobOrder:cronjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes Expired Order Files from Storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        $i = 0;

        $files = Storage::disk('public')->allFiles('order_files');

        foreach ($files as $file) {
        $time = Storage::disk('public')->lastModified($file);
        $fileModifiedDateTime = Carbon::parse($time);
            
        if (Carbon::now()->gt($fileModifiedDateTime->addHour(12))) {   
                $response = Storage::disk('public')->delete($file);
                $i++;
            }            
        }            
        // dd(@$response, $i);
        // return true;
        if($i > 0) {
                $this->info('Order files removed Successfully!');
                Log::info($i.' Order Files Removed Successfully and Cron has ended');
        } else { 
                Log::info('No pending Order files and Cron has ended');
        }
  }

}
