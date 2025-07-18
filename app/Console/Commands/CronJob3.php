<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Front\HDFCRazorpayController;
use Razorpay\Api\Api;

class CronJob3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronJob3:cronjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Pending HDFC RazorPay Payments';

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
        $payments = DB::table('HDFCPayments')->where('status', 'created')->where('created_at', '>=', now()->subHours(12)->toDateTimeString())->where('created_at', '<=', now()->subMinutes(10)->toDateTimeString())->orderBy('created_at', 'DESC')->get();
        // $payment = DB::table('HDFCPayments')->where(function($query) {
        // $query->where('status','created')->orWhere('status','progress');
        // })->where('created_at', '>=', now()->subHours(12)->toDateTimeString())->where('created_at', '<=', now()->subMinutes(10)->toDateTimeString())->orderBy('created_at','DESC')->get();        
        // dd($payments);
        $i = 0;
        foreach (@$payments as $payment) {
            if (@$payment->razorpay_order_id) {
                $api = new Api(env('HDFC_RAZORPAY_KEY'), env('HDFC_RAZORPAY_SECRET'));
                // $order = $api->order->fetch(@$payment->razorpay_order_id)->payments();
                $order = $api->order->fetch(@$payment->razorpay_order_id);
                if (@$order->attempts > 0) {
                    // dd($order);
                    $request = new Request([
                        'razorpay_payment_id' => null,
                        'razorpay_order_id' => $payment->razorpay_order_id,
                        'token' => '',
                        'process' => 'cronManual',
                    ]);
                    $response[] = HDFCRazorpayController::payment($request);
                    $i++;
                }
            }
        }
        // dd(@$response, $i);
        // return true;
        if ($i > 0) {
            $this->info('Payment Processed Successfully!');
            Log::info($i . ' HDFC RazorPay Payments Processed Successfully and Cron has ended');
        } else {
            Log::info('No pending HDFC RazorPay Payments and Cron has ended');
        }
    }
}

