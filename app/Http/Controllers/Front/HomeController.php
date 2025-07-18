<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use Auth;
use DB;
use Session;
use Redirect;
use Spatie\Browsershot\Browsershot;
use PDF;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = 'Dashboard';

        return view('front.dashboard', compact('title'));
    } 

    public function printReceipt(Request $request)
    {
        // dd($request->all());

        $past_receipt = DB::select("EXEC ibt_SMS_GetStudentReceipts @StudentID=?, @AccountHeadID=?", array( $request->id, 1 ));

        $view = view('pdf.receipt', compact('past_receipt','data'));
        $pdfFileName = @$data->CampusID.'receipt.pdf';
        header('Content-type:application/pdf');
        header('Content-disposition: inline; filename="' . $pdfFileName . '"');
        echo Browsershot::html($view->render())->format('A4')->noSandbox()->pdf();
        exit;

        // $data = ['past_receipt' => $past_receipt, 'data' => $data];
        // $pdf = PDF::loadView('pdf.receipt', $data);
        // return $pdf->setPaper('a4', 'landscape')->inline(@$data->CampusID.'receipt.pdf');

    }

}
