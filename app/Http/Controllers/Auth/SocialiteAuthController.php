<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Socialite;
use Illuminate\Support\Facades\Auth;
use Hash;
use DB;
use Session;
use Redirect;
use Exception;

class SocialiteAuthController extends Controller
{
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google login authentication
     *
     * @return void
     */
    public function loginWithGoogle()
    {
        try {

            $googleUser = Socialite::driver('google')->user();

            // dd($googleUser);

            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if($user){
                if($user->google_id == null)
                {
                    $user->google_id = $googleUser->id;
                    $user->verified = '1';
                    $user->update();
                }
                Auth::login($user);
                return redirect('/dashboard');
            }

            else{

             $data = DB::table('LibraryUsers')
             ->select('LibraryUsers.ID as CampusID', 'Students.*')
             ->join('Students', 'Students.ID', '=', 'LibraryUsers.ReferenceID')
             ->where('LibraryUsers.LibraryUserTypeID', '3')
             ->where(function($query) use($googleUser){
                $query->where('Students.Email', '=', $googleUser->email)
                ->orWhere('Students.FatherEmail', '=', $googleUser->email)
                ->orWhere('Students.MotherEmail', '=', $googleUser->email);
            })
             ->first();

             // dd($data);

             if($data){

                if($googleUser->email == $data->Email)
                {
                    $register_by = 'STUDENT';
                }else {
                    $register_by = 'PARENT';
                }

                $createUser = User::create([

                    'username' => $data->CampusID,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'register_by' => $register_by,
                    'register_type' =>'EMAIL',
                    'verified' => '1',
                    'password' => Hash::make('test@123')
                ]);

                // dd($createUser);

                Auth::login($createUser);
                return redirect('/dashboard');
            }
            else
            {
                Session::flash('error', "Data not found. <br/>Please Contact IT for more info...");
                return Redirect::route('login');
            }
        }


    } catch (Exception $exception) {
        dd($exception->getMessage());
    }
}
}