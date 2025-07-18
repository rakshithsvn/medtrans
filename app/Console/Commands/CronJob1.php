<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Front\CashfreeController;

class CronJob1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronJob1:cronjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Pending CashFree Payments';

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
        //$payments = DB::table('CF_Payments')->where('order_status', 'ACTIVE')->whereDate('created_at', '>=', date('2022-08-01'))->whereDate('created_at', '<=', date('2022-08-01'))->orderBy('created_at','DESC')->get();
	$payments = DB::table('CF_Payments')->where('order_status', 'ACTIVE')->where('created_at', '>=', now()->subHours(12)->toDateTimeString())->where('created_at', '<=', now()->subMinutes(15)->toDateTimeString())->orderBy('created_at','DESC')->get();
	//dd($payments);
            $i = 0;
            foreach(@$payments as $payment) {
                if(@$payment->cf_order_id) {                   
                    $request = new Request([
                        'order_id' => $payment->cf_order_id,
                        'process' => 'cronManualCF',
                    ]);
                    $response[] = CashfreeController::CFPayment($request);
                    $i++; 
                }
            }
        // dd($this->response_data_arr);
        // return true;
      $this->info($i.' Payment Processed Successfully!');
      Log::info($i.' CashFree Payments Processed Successfully and Cron has ended');

  }

}

