<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialiteAuthController;
use App\Http\Controllers\Back\HomeController as BackHomeController;

use App\Http\Controllers\Back\VehicleController;
use App\Http\Controllers\Back\AmbulanceController;
use App\Http\Controllers\Back\TransportController;
use App\Http\Controllers\Back\HomeHealthController;
use App\Http\Controllers\Back\EmployeeController;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/connection', function () {
    return view('welcome');
});

Route::get('/test-notification', function () {
    $user = \App\Models\User::first();
    $user->notify(new GeneralNotification(
        '🔔 Test Notification',
        url('/dashboard')
    ));

    return 'Notifications sent to ' . $user->name;
});

Route::get('/notifications/read/{id}', function ($id) {
    $notification = auth()->user()->notifications()->findOrFail($id);

    $notification->markAsRead();

    $redirectUrl = $notification->data['url'] ?? '/dashboard';

    return redirect($redirectUrl);
})->name('notifications.read');

Auth::routes();

Route::get('/', [AuthController::class, 'index']);

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('post-login');
// Route::get('register', [AuthController::class, 'registration'])->name('register');
// Route::post('register', [AuthController::class, 'postRegistration'])->name('post-register');

Route::middleware(['auth'])->group(function () {

    Route::match(['get', 'post'], 'dashboard', [BackHomeController::class, 'index'])->name('dashboard');

    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        Route::match(['get', 'post'], '/', [VehicleController::class, 'index'])->name('index');
        Route::post('/store', [VehicleController::class, 'store'])->name('store');
        Route::post('/{vehicle}', [VehicleController::class, 'update'])->name('update');
        Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');
        Route::post('/import', [VehicleController::class, 'import'])->name('import');
    });

    Route::get('/api/search-vehicles', [VehicleController::class, 'search'])->name('vehicles.search');
    Route::get('/vehicle-docs/{vehicleId}', [VehicleController::class, 'getVehicleDocs']);

    Route::prefix('vehicle-movements')->name('vehicle-movements.')->group(function () {
        Route::match(['get', 'post'], '/', [VehicleController::class, 'movementIndex'])->name('index');
        Route::post('/store', [VehicleController::class, 'movementStore'])->name('store');
        Route::post('/{vehicle}', [VehicleController::class, 'movementUpdate'])->name('update');
        Route::delete('/{vehicle}', [VehicleController::class, 'movementDestroy'])->name('destroy');
        Route::post('/import', [VehicleController::class, 'movementImport'])->name('import');
    });

    Route::post('trip-cancel/{id}', [VehicleController::class, 'movementCancel'])->name('trip-cancel');
    Route::post('trip-cancel/{id}/request', [VehicleController::class, 'RequestCancel'])->name('trip-cancel.request');

    Route::get('/vehicles/summary/{type}', [VehicleController::class, 'vehicleSummary'])->name('vehicles.summary');

    Route::get('/vehicle-move-data/{vehicleId}', [VehicleController::class, 'getVehicleMoveData']);
    Route::get('/vehicle-fuel-data/{vehicleId}', [VehicleController::class, 'getVehicleFuelData']);
    Route::get('/vehicle-service-data/{vehicleId}', [VehicleController::class, 'getVehicleServiceData']);

    Route::prefix('vehicle-fuels')->name('vehicle-fuels.')->group(function () {
        Route::match(['get', 'post'], '/', [VehicleController::class, 'fuelIndex'])->name('index');
        Route::post('/store', [VehicleController::class, 'fuelStore'])->name('store');
        Route::post('/{vehicle}', [VehicleController::class, 'fuelUpdate'])->name('update');
        Route::delete('/{vehicle}', [VehicleController::class, 'fuelDestroy'])->name('destroy');
        Route::post('/import', [VehicleController::class, 'fuelImport'])->name('import');
    });

    Route::prefix('vehicle-checklist')->name('vehicle-checklist.')->group(function () {
        Route::match(['get', 'post'], '/', [VehicleController::class, 'checklistIndex'])->name('index');
        Route::post('/store', [VehicleController::class, 'checklistStore'])->name('store');
        Route::post('/{vehicle}', [VehicleController::class, 'checklistUpdate'])->name('update');
        Route::delete('/{vehicle}', [VehicleController::class, 'checklistDestroy'])->name('destroy');
        Route::post('/import', [VehicleController::class, 'checklistImport'])->name('import');
    });

    Route::prefix('vehicle-jobs')->name('vehicle-jobs.')->group(function () {
        Route::match(['get', 'post'], '/', [VehicleController::class, 'jobIndex'])->name('index');
        Route::post('/store', [VehicleController::class, 'jobstore'])->name('store');
        Route::post('/{vehicle}', [VehicleController::class, 'jobUpdate'])->name('update');
        Route::delete('/{vehicle}', [VehicleController::class, 'jobDestroy'])->name('destroy');
        Route::post('/import', [VehicleController::class, 'jobImport'])->name('import');
        Route::get('/pdf/{id}', [VehicleController::class, 'downloadPdf'])->name('pdf');
    });

    Route::prefix('ambulance')->name('ambulance.')->group(function () {
        Route::match(['get', 'post'], '/request/{type}', [AmbulanceController::class, 'index'])->name('index');
        Route::post('/store', [AmbulanceController::class, 'store'])->name('store');
        Route::post('/update/{request}', [AmbulanceController::class, 'update'])->name('update');
        Route::delete('/{request}', [AmbulanceController::class, 'destroy'])->name('destroy');
        Route::post('/import', [AmbulanceController::class, 'import'])->name('import');
        Route::post('/allocate', [AmbulanceController::class, 'allocate'])->name('allocate');
        Route::match(['get', 'post'], '/report', [AmbulanceController::class, 'report'])->name('report');
    });
    
    Route::prefix('technician')->name('technician.')->group(function () {
        Route::match(['get', 'post'], 'request/{type}', [AmbulanceController::class, 'techIndex'])->name('index');
        Route::post('/store', [AmbulanceController::class, 'techStore'])->name('store');
        Route::post('/update/{request}', [AmbulanceController::class, 'techUpdate'])->name('update');
    });

    Route::prefix('transport')->name('transport.')->group(function () {
        Route::match(['get', 'post'], '/request', [TransportController::class, 'index'])->name('index');
        Route::post('/store', [TransportController::class, 'store'])->name('store');
        Route::post('/update/{request}', [TransportController::class, 'update'])->name('update');
        Route::delete('/{request}', [TransportController::class, 'destroy'])->name('destroy');
        Route::post('/import', [TransportController::class, 'import'])->name('import');
        Route::post('/allocate', [TransportController::class, 'allocate'])->name('allocate');
        Route::match(['get', 'post'], '/report', [TransportController::class, 'report'])->name('report');
    });

    Route::prefix('home-health')->name('home-health.')->group(function () {
        Route::match(['get', 'post'], '/request', [HomeHealthController::class, 'index'])->name('index');
        Route::post('/store', [HomeHealthController::class, 'store'])->name('store');
        Route::post('/update/{request}', [HomeHealthController::class, 'update'])->name('update');
        Route::delete('/{request}', [HomeHealthController::class, 'destroy'])->name('destroy');
        Route::post('/import', [HomeHealthController::class, 'import'])->name('import');
        Route::post('/allocate', [HomeHealthController::class, 'allocate'])->name('allocate');
        Route::match(['get', 'post'], '/report', [HomeHealthController::class, 'report'])->name('report');
    });

    Route::match(['get', 'post'], 'employee/{type}', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee-store', [EmployeeController::class, 'store'])->name('employee.store');
    Route::post('/employee-update/{employee}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::delete('/employee/{employee}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

    Route::get('reports', [BackHomeController::class, 'reports'])->name('reports');
    Route::match(['get', 'post'], 'reports/ambulance', [BackHomeController::class, 'reportAmbulance'])->name('reports.ambulance');
    Route::match(['get', 'post'], 'reports/vehicle', [BackHomeController::class, 'reportVehicle'])->name('reports.vehicle');
    Route::match(['get', 'post'], 'reports/driver', [BackHomeController::class, 'reportDriver'])->name('reports.driver');
    Route::match(['get', 'post'], 'reports/department', [BackHomeController::class, 'reportDepartment'])->name('reports.department');

    Route::match(['get', 'post'], 'prod-report', [BackHomeController::class, 'prodReport'])->name('prod-report');
    Route::match(['get', 'post'], 'patient-report', [BackHomeController::class, 'patientReport'])->name('patient-report');
    Route::match(['get', 'post'], 'ambulance-report', [BackHomeController::class, 'ambulanceReport'])->name('ambulance-report');

    Route::get('/get-ambulance-data/{type?}', [BackHomeController::class, 'getAmbulanceData']);
    Route::get('/get-vehicle-data/{type?}', [BackHomeController::class, 'getVehicleData']);
    Route::get('/get-driver-data/{type?}', [BackHomeController::class, 'getDriverData']);
    Route::get('/get-dept-data/{type?}', [BackHomeController::class, 'getDeptData']);

});

Route::get('privacy-policy', [AuthController::class, 'privacyPolicy'])->name('privacy-policy');

Route::get('google', [SocialiteAuthController::class, 'googleRedirect'])->name('auth/google');
Route::get('/auth/google-callback', [SocialiteAuthController::class, 'loginWithGoogle']);

// Admin

Route::get('admin-login', [AuthController::class, 'index'])->name('admin-login');
Route::post('admin-login', [AuthController::class, 'postLogin'])->name('post-admin-login');

Route::get('authRegister', [AuthController::class, 'registration'])->name('authRegister');
Route::post('authRegister', [AuthController::class, 'postRegistration'])->name('post-authRegister');

Route::get('admin/forgot-password', [AuthController::class, 'forgotPassword'])->name('admin-forgot-password');
Route::post('admin/post-forgot-password', [AuthController::class, 'postForgotPassword'])->name('admin/post-forgot-password');
Route::post('admin/forgot-password/verification-code', [AuthController::class, 'forgotPasswordVerificationCode'])->name('admin/forgot-password-verification-code');
Route::post('admin/forgot-password-resend-verification-code', [AuthController::class, 'forgotPasswordResendVerificationCode'])->name('admin/forgot-password-resend-verification-code');

Route::get('admin/reset-password', [AuthController::class, 'resetPassword'])->name('admin/reset-password');
Route::post('admin/reset-password', [AuthController::class, 'postResetPassword'])->name('admin/post-reset-password');

Route::middleware(['auth', 'is_verify_email'])->group(function () {

    Route::get('admin-logout', [AuthController::class, 'logout'])->name('admin-logout');
    Route::get('admin/change-password', [AuthController::class, 'changePassword'])->name('admin/change-password');
    Route::post('admin/change-password', [AuthController::class, 'postChangePassword'])->name('admin/post-change-password');

    Route::get('admin/profile/{id?}', [BackHomeController::class, 'profile'])->name('profile');

    Route::get('admin/settings', function () {
        return view('back.settings');
    })->name('settings');
    Route::get('admin/support', function () {
        return view('back.support');
    })->name('support');

    Route::get('admin/modules', [BackHomeController::class, 'modules'])->name('modules');
    Route::post('admin/update-module', [BackHomeController::class, 'storeModule'])->name('admin/update-module');

    Route::get('admin/roles', [BackHomeController::class, 'roles'])->name('roles');
    Route::post('admin/update-role', [BackHomeController::class, 'storeRole'])->name('admin/update-role');

    Route::get('admin/users', [BackHomeController::class, 'users'])->name('users');
    Route::post('admin/users', [BackHomeController::class, 'filterUsers'])->name('post-users');

    Route::get('admin/user-verify/{id?}', [BackHomeController::class, 'userVerify'])->name('user-verify');
    Route::post('admin/reset-password', [BackHomeController::class, 'postResetPassword'])->name('admin-reset-password');

    Route::post('admin/update-user', [BackHomeController::class, 'updateUser'])->name('admin/update-user');

    Route::get('admin/query', [BackHomeController::class, 'adminRunQuery'])->name('get.admin.query');
    Route::post('admin/query', [BackHomeController::class, 'submitAdminRunQuery'])->name('post.admin.query');
});

Route::get('errorpage',function(){
    return view('errors.404');
});

