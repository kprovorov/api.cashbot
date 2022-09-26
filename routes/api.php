<?php

use App\AccountModule\Controllers\AccountController;
use App\AccountModule\Controllers\JarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RateController;
use App\PaymentModule\Controllers\PaymentController;
use App\TransferModule\Controllers\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('jars', JarController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('transfers', TransferController::class);
    Route::apiResource('groups', GroupController::class);

    Route::get('dashboard', DashboardController::class);
    Route::get('rates', RateController::class);
});

