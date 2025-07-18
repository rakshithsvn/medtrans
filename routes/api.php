<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Back\APIController;
use App\Http\Middleware\VerifyToken;
// use App\Http\Controllers\Back\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('api/v1')->group(function () {

    Route::post('register', [APIController::class, 'register'])->name('register');
    Route::post('auth', [APIController::class, 'login'])->name('auth');

    Route::middleware(['verifyToken'])->group(function () {
        Route::get('get-master-list', [APIController::class, 'getMasterList'])->name('getMasterList');
        Route::get('get-vehicle-list', [APIController::class, 'getVehicleList'])->name('getVehicleList');
        Route::get('get-driver-list', [APIController::class, 'getDriverList'])->name('getDriverList');
        Route::get('get-trip-request', [APIController::class, 'getTripRequest'])->name('getTripRequest');
        Route::get('get-trip-completed', [APIController::class, 'getTripCompleted'])->name('getTripCompleted');
        Route::get('get-fuel-consumption', [APIController::class, 'getFuelConsumption'])->name('getFuelConsumption');
        Route::get('get-vehicle-checklist', [APIController::class, 'getVehicleChecklist'])->name('getVehicleChecklist');
        Route::post('trip-update', [APIController::class, 'tripUpdate'])->name('tripUpdate');
        Route::post('fuel-update', [APIController::class, 'fuelUpdate'])->name('fuelUpdate');
        Route::post('checklist-update', [APIController::class, 'checklistUpdate'])->name('checklistUpdate');
        Route::post('logout', [APIController::class, 'logout'])->name('logout');
    });


    Route::get('getUsers', [APIController::class, 'getUsers'])->name('getUsers');
    Route::get('getProducts', [APIController::class, 'getProducts'])->name('getProducts');

    Route::middleware('verifyToken')->get('getUserDetail', [APIController::class, 'getUserDetail'])->name('getUserDetail');
    Route::middleware('verifyToken')->get('getDealers', [APIController::class, 'getDealers'])->name('getDealers');
    Route::middleware('verifyToken')->get('getHelpMessages', [APIController::class, 'getHelpMessages'])->name('getHelpMessages');
    Route::middleware('verifyToken')->get('getPurchaseList', [APIController::class, 'getPurchaseList'])->name('getPurchaseList');
    Route::middleware('verifyToken')->get('getRewardPoints', [APIController::class, 'getRewardPoints'])->name('getRewardPoints');

    Route::middleware('verifyToken')->post('postAddDealer', [APIController::class, 'postAddDealer'])->name('postAddDealer');
    Route::middleware('verifyToken')->post('postAddProduct', [APIController::class, 'postAddProduct'])->name('postAddProduct');
    Route::middleware('verifyToken')->post('addPurchaseEntry', [APIController::class, 'addPurchaseEntry'])->name('addPurchaseEntry');
    Route::middleware('verifyToken')->post('claimRewards', [APIController::class, 'claimRewards'])->name('claimRewards');

    Route::middleware('verifyToken')->post('uploadMedia', [APIController::class, 'uploadMedia'])->name('uploadMedia');

    Route::post('postMessage', [APIController::class, 'postMessage'])->name('postMessage');
    Route::post('postGroupMessage', [APIController::class, 'postGroupMessage'])->name('postGroupMessage');

    Route::get('getUserChatList', [APIController::class, 'getUserChatList'])->name('getUserChatList');
    Route::get('getUserMessages', [APIController::class, 'getUserMessages'])->name('getUserMessages');
    Route::get('getGroupMessages', [APIController::class, 'getGroupMessages'])->name('getGroupMessages');
    Route::post('postDeviceToken', [APIController::class, 'postDeviceToken'])->name('postDeviceToken');


    Route::middleware('verifyToken')->get('getEvent', [APIController::class, 'getEvent'])->name('getEvent');
    Route::middleware('verifyToken')->get('getEventAgenda', [APIController::class, 'getEventAgenda'])->name('getEventAgenda');
    Route::middleware('verifyToken')->get('getEventSpeakers', [APIController::class, 'getEventSpeakers'])->name('getEventSpeakers');
    Route::middleware('verifyToken')->get('getEventUsers', [APIController::class, 'getEventUsers'])->name('getEventUsers');


    Route::get('getSpeaker', [APIController::class, 'getSpeaker'])->name('getSpeaker');
    Route::get('getNotification', [APIController::class, 'getNotification'])->name('getNotification');
    Route::get('getGallery', [APIController::class, 'getGallery'])->name('getGallery');

    // Route::post('send-notification', [NotificationController::class, 'send']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
