<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Mail;

class CronJob2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronJob2:cronjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Absentees Detail Mail to Warden ';

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
        $role = DB::table('roles')->where('slug','warden')->first();
        $wardens = DB::table('user_roles')->where('role_id', $role->id)->get();
        foreach(@$wardens as $warden) {
        if(@$warden->block_id) {
            $query = DB::table('StayLocationsNew')->join('StayLocationBlocks', 'StayLocationBlocks.StayLocationID','=','StayLocationsNew.ID')->where('StayLocationBlocks.ID', $warden->block_id)->first();
            $hostel = ['punch' => $query->punch,'time1' => $query->time1,'time2' => $query->time2,'time3' => $query->time3,'time4' => $query->time4];
            $student_list =  DB::table('LibraryUsers')            
                ->join('Students', 'Students.ID', '=', 'LibraryUsers.ReferenceID')
                ->where('LibraryUsers.LibraryUserTypeID', '3')->whereNotNull('Students.RoomID')->limit('10')->pluck('LibraryUsers.ID as CampusID')->toArray();
            @$date = now()->format('Y-m-d');
            $results = DB::connection('sqlsrv1')->table(env('DB_TABLE', 'forge'))->whereDate('LogDate', @$date)->whereIn('UserId', @$student_list)->orderBy('DeviceLogId', 'DESC')->get();
            
            $bioResult = $results ? collect($results)->groupBy('UserId'):[];

            if(count(@$bioResult)) {
                $presen = 0 ; $absent = 0;
                foreach($student_list as $student) {                
                    if(!$bioResult->has($student)) {
                        $student_abs[] = $student;
                        $absent++;
                    } else {
                        $present++;
                    } 
                }
            } else {
                $student_abs = $student_list;
                $absent++;
            } 

            $total = count($student_list);
            $present_count = $present;
            $absent_count = $absent;

            Mail::send('email.WardenAttReport', ['warden' => $warden], function($message) use($warden){
                $message->to($warden->email);
                $message->subject('Hostel Attendance Report');
            });
            }
        // return true;
      $this->info('Mail Sent Successfully!');
      Log::info('Absentees Mail Sent Successfully and Now cron has ended');

  }

}
}
