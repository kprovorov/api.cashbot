<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\TransferController;
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

Route::apiResource('accounts', AccountController::class)->middleware('auth:sanctum');
Route::apiResource('payments', PaymentController::class)->middleware('auth:sanctum');
Route::apiResource('transfers', TransferController::class)->middleware('auth:sanctum');
Route::apiResource('groups', GroupController::class)->middleware('auth:sanctum');

Route::get('rates', RateController::class)->middleware('auth:sanctum');
