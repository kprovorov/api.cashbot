<?php

use App\AccountModule\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RateController;
use App\PaymentModule\Controllers\PaymentController;
use App\UserModule\Controllers\UserController;
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
    //    Route::apiResource('users', UserController::class);

    Route::apiResource('accounts', AccountController::class)->only([
        'index',
        'update',
    ]);
    Route::apiResource('payments', PaymentController::class);
    Route::delete('payments/groups/{group}', [PaymentController::class, 'deleteGroup']);
    Route::put('payments/{payment}/general', [PaymentController::class, 'updateGeneral']);

    Route::get('rates', RateController::class);
});
