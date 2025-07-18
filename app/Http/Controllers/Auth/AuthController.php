<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Models\User;
use App\Models\UserVerify;
use App\Models\Employee;
use App\Models\Patient;
use Hash;
use Illuminate\Support\Str;
use Mail;
use Redirect;
use DB;
use Illuminate\Validation\Rules\Password;
use App\Classes\SMSProvider;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function registration()
    {
        return view('auth.register');
    }

    public function postLogin(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'register_by' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user_role = DB::table('user_roles')->where('user_id', @$user->id)->first();
            $role = DB::table('roles')->where('id', @$user_role->role_id)->first();
            
            if(in_array(@$role->slug, ['admin', 'supervisor'])) {   
                return redirect()->intended('dashboard');
            } else if(@$role->slug == 'transport') {           
                return redirect()->intended('transport/request');
            } else if(@$role->slug == 'ward') {           
                return redirect()->intended('ambulance/request/ward');
            } else if(@$role->slug == 'helpdesk') {           
                return redirect()->intended('ambulance/request/help-desk');
            } else if(@$role->slug == 'homehealth') {           
                return redirect()->intended('home-health/request');
            } else if(@$role->slug == 'technician') {           
                return redirect()->intended('technician/request/ward');
            } else if(@$role->slug == 'driver') {           
                return redirect()->intended('vehicle-movements');
            }           
        } else {
            Session::flash('error', "Oops! You have entered invalid credentials");
            return redirect()->route('admin-login');
        }
    }

    public function postRegistration(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'name'=> ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:user_login'],
            // 'mobile_number' => ['string', 'max:10'],
            'register_type' => ['required'],
            'register_by' => ['required'],
            'password' => ['required',
                            // Password::min(6)
                            // ->mixedCase()
                            // ->numbers()
                            // ->symbols(),
                            // ->uncompromised(),
                            // 'min:6',
                            // 'regex:/[a-z]/',      // must contain at least one lowercase letter
                            // 'regex:/[A-Z]/',      // must contain at least one uppercase letter
                            // 'regex:/[0-9]/',      // must contain at least one digit
                            // 'regex:/[@$!%*#?&]/', // must contain a special character
            'confirmed'],
        ]);

        $existingUserCount = User::where('username', $request->username)->where('register_by', $request->register_by)->where('verified', true)->count();

        if($existingUserCount > 0) {
            Session::flash('error', "Already Registered. <br/>Please Login to Continue...");
            return false;

        }

        if($request->password !== $request->password_confirmation) {          
            Session::flash('error', "Password & Confirm Password don\'t match");
            return false;
        }

        $data = $request->all();
        $confirmationCode = bin2hex(random_bytes(32));

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'mobile_number' => isset($data['mobile']) ? $data['mobile'] : null,
            'register_by' => $data['register_by'],
            'register_type' => $data['register_type'],
            'password' => Hash::make($data['password']),
            'confirmation_code' => $confirmationCode,
            'verified' => true,
        ]);

        // UserVerify::create([
        //     'user_id' => $user->id,
        //     'token' => $confirmationCode
        // ]);

        // if($request->register_type === 'EMAIL') {
        //     Mail::send('email.emailVerification', ['token' => $confirmationCode, 'register_by' => $request->register_by], function($message) use($request, $email){
        //         $message->to($email);
        //         $message->subject('Verify Email');
        //     });
        // }

        // Session::flash('success', "Verification email sent to ".$this->obfuscate_email($email).". <br/>Please check your email to verify your account and complete the registration process.");

        // return Redirect::route('login');
        return true;
    }

    public function getOtp() {
        return view('sms-verification');
    }

    public function verifyMobileNumber(Request $request)
    {
        $user = User::where('username', $request->username)->where('confirmation_code', $request->otp)->where('register_by', $request->user_type)->where('verified', false)->first();
        if($user) {
            $user->verified = true;
            $user->confirmation_code = null;
            $user->save();

            return ['status' => 'success', 'message' => 'Mobile number verified! Please login'];
        } else {
            return ['status' => 'error', 'message' => 'Invalid Verification Code Entered! Please try again'];
        }
    }

    public function dashboard()
    {
        if(Auth::check()){

            $user = Auth::user();

            // if($user->register_by == 'EMPLOYEE')
            // {
            //     $title = 'Employee Dashboard';

            //     $data = DB::table('LibraryUsers')
            //     ->select('LibraryUsers.ID as CampusID', 'Students.*')
            //     ->join('Students', 'Students.ID', '=', 'LibraryUsers.ReferenceID')
            //     ->where('LibraryUsers.LibraryUserTypeID', '3')
            //     ->where('LibraryUsers.ID', $user->username)
            //     ->first();

            //     return view('back.profile' ,compact('title','user','data'));
            // } else {

            $title = 'Dashboard';

            $users = User::where('register_by','!=','ADMIN')->where('register_by','!=','EMPLOYEE')->orderBy('id')->get();

            $employees = Employee::get();
            $patients = Patient::get();

            return view('back.dashboard' ,compact('title','users', 'employees','patients'));
            // }
        }

        return redirect("login")->withSuccess('Oops! You do not have access');
    }

    public function create(array $data)
    {
    $username = $data['register_type'] === 'MOBILE' ? $data['mobile'] : $data['email'];

    return User::create([
        'name' => $data['name'],
        'email' => isset($data['email']) ? $data['email'] : null,
        'mobile_number' => isset($data['mobile']) ? $data['mobile'] : null,
        'username' => $username,
        'register_by' => $data['register_by'],
        'register_type' => $data['register_type'],
        'password' => Hash::make($data['password'])
    ]);
    }

    public function logout() {
        Session::flush();
        Auth::logout();

        // if(@$user->register_by == 'ADMIN') {
        return redirect()->route('login');
        // } else {
        //     return redirect()->route('login',['register_by'=>@$user->register_by]);
        // }
    }

    public function verifyAccount($token)
    {
        $verifyUser = UserVerify::where('token', $token)->first();

        $message = 'Sorry, Verification link has been expired.';

        if(!is_null($verifyUser) ){
            $user = $verifyUser->user;

            if(!$user->is_email_verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $message = "Your e-mail is verified. You can now login.";
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }

        return redirect()->route('login')->with('message', $message);
    }

    public function registrationResendVerificationCode(Request $request)
    {
        $user = User::where('username', $request->username)->where('register_by', $request->user_type)->where('verified', false)->first();
        $message = "Dear User, Your Verification Code is $user->confirmation_code. With Regards";

        $request = new Request;
        $request['mobileNumber'] = $user->mobile_number;
        $request['message'] = $message;
        app('App\Http\Controllers\NexmoSMSController')->sendSMS($request);
        return ['status' => 'success', 'message' => 'Verification Code Re-Sent'];
    }

    public function forgotPassword(Request $request)
    {
        return view('forgot-password');
    }

    public function postForgotPassword(Request $request)
    {
        // dd($request->all());

        $user = User::where('username', $request->username)->where('register_by', $request->user_type)->where('verified', true)->first();

        if($request->login_type === 'EMAIL') {

            if(!$user) {
                Session::flash("error", "Provided ID is not registered with us");
                return Redirect::back();
            }
            $user->confirmation_code = bin2hex(random_bytes(32));
            $user->save();

            $token = $user->confirmation_code;
            Mail::send('email.reset-password-email', [ 'link' => url('/admin/reset-password?username=' . $user->username . '&user_type=' . $request->user_type . '&verification_code=' . $token) ], function($message) use($user) {
                $message->to($user->email);
                $message->subject('Reset Password');
            });

            Session::flash('success', "Reset password email sent to ".$this->obfuscate_email($user->email).". <br/>Please check your email to reset password of your account.");
            return Redirect::route('login');
        }

        if($request->login_type === 'MOBILE') {

        if($request->user_type == 'PARENT')
        {
            $user = User::where('mobile_number', $request->FatherNumber)->orWhere('mobile_number', $request->MotherNumber)->where('verified', true)->first();
        }
        else
        {
            $user = User::where('mobile_number', $request->mobile)->where('verified', true)->first();
        }

        if(!$user) {
            Session::flash("error", "Provided mobile number is not registered with us");
            return Redirect::back();
        }

        $string = '0123456789';
        $stringShuffled = str_shuffle($string);
        $user->confirmation_code = substr($stringShuffled, 1, 5);
        $user->save();
        $message = "Dear User, Your Verification Code is $user->confirmation_code. Regards ";

        $request = new Request;
        $request['mobileNumber'] = $user->mobile_number;
        $request['message'] = $message;
        app('App\Http\Controllers\NexmoSMSController')->sendSMS($request);
        return view('forgot-password-verification-code')->with('success', 'Verification Code Sent!')->with('username', $user->username);
    }

    }

    public function changePassword() {
        $title = 'Settings';
        return view('back.change-password', compact('title'));
    }

    public function postChangePassword(Request $request)
    {
        if ($request->current_password && $request->new_password && $request->new_password_confirm) {
            $user = Auth::user();
            if (Hash::check($request->current_password, $user->password)) {
                if ($request->new_password !== $request->new_password_confirm) {
                    Session::flash("error", "New Password and Confirm Password don't match");
                    return Redirect::back();
                }
                $user->password = Hash::make($request->new_password);
                $user->save();

                Session::flash("success", "Password Updated");
                return Redirect::back();
            } else {
                Session::flash("error", "Incorrect Current Password");
                return Redirect::back();
            }
        } else {
            Session::flash("error", "No Data Given");
            return Redirect::back();
        }
    }

    public function resetPassword(Request $request)
    {
        if($request->username && $request->user_type && $request->verification_code) {

            $user = User::where('username', $request->username)->where('register_by', $request->user_type)->where('verified', true)->where('confirmation_code', $request->verification_code)->first();

            if(!$user) {
                return redirect()->back()->withError('Invalid Password Reset Link');
            }

            return view('reset-password');
        } else {
            return redirect()->back()->withError('Invalid Password Reset Link');
        }
    }

    public function postResetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required',
                            Password::min(6)
                            ->mixedCase()
                            ->numbers()
                            ->symbols()
                            ->uncompromised(),
                            // 'min:6',
                            // 'regex:/[a-z]/',      // must contain at least one lowercase letter
                            // 'regex:/[A-Z]/',      // must contain at least one uppercase letter
                            // 'regex:/[0-9]/',      // must contain at least one digit
                            // 'regex:/[@$!%*#?&]/', // must contain a special character
            'confirmed'],
        ]);

        if($request->username && $request->user_type && $request->verification_code) {

            $user = User::where('username', $request->username)->where('register_by', $request->user_type)->where('verified', true)->where('confirmation_code', $request->verification_code)->first();
            if(!$user) {
                return redirect()->route('dashboad')->withError('Invalid Password Reset Link');
            }
            if($request->password === $request->password_confirm) {
                $user->password = Hash::make($request->password);
                $user->confirmation_code = null;
                $user->save();
                return redirect()->route('login')->withSuccess('Password Reset! Please login');
            } else {
                return redirect()->back()->withError('New Password and Confirm Password don\'t match');
            }
        } else {
            return redirect()->route('home')->withError('Invalid Password Reset Link');
        }
    }

    public function forgotPasswordVerificationCode(Request $request)
    {
        if($request->username && $request->user_type && $request->verification_code) {

            $user = User::where('username', $request->username)->where('register_by', $request->user_type)->where('verified', true)->where('confirmation_code', $request->verification_code)->first();

            if(!$user) {
                return view('forgot-password-verification-code')->with('error', 'Incorrect Verification Code Entered!')->with('username', $request->username);
            }

            return redirect()->route('reset-password', [
                'username' => $request->username,
                'register_by' => $request->user_type,
                'verification_code' => $request->verification_code
            ]);
        }
    }

    public function forgotPasswordResendVerificationCode(Request $request)
    {
        $user = User::where('username', $request->username)->where('register_by', $request->user_type)->where('verified', true)->first();
        $message = "Dear User, Your Verification Code is $user->confirmation_code. Regards ";

        $request = new Request;
        $request['mobileNumber'] = $user->mobile_number;
        $request['message'] = $message;
        app('App\Http\Controllers\NexmoSMSController')->sendSMS($request);

        return view('forgot-password-verification-code')->with('success', 'Verification Code Re-Sent!')->with('username', $user->username, 'user_type', $user->user_type);
    }

    public function obfuscate_email($email)
    {
        $em   = explode("@",$email);
        $name = implode('@', array_slice($em, 0, count($em)-1));
        $len  = floor(strlen($name)/2);

        return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
    }
	
	public function privacyPolicy()
    {
        $title = 'Privacy Policy';
        return view('back.privacy-policy', compact('title'));
    }

}
