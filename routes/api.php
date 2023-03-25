<?php

use App\AccountModule\Controllers\AccountController;
use App\Http\Controllers\RatesController;
use App\PaymentModule\Controllers\PaymentController;
use App\UserModule\Controllers\UserController;
use App\UserModule\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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

// Sanctum token
Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    // Admin
    Route::group(['middleware' => 'admin'], function () {
        // Users
        Route::apiResource('users', UserController::class)->only([
            'index',
            'store',
        ]);
    });

    // Misc
    Route::get('rates', RatesController::class);

    // Accounts
    Route::apiResource('accounts', AccountController::class);

    // Payments
    Route::apiResource('payments', PaymentController::class)->only([
        'store',
        'show',
        'destroy',
    ]);
    Route::delete('payments/groups/{group}', [PaymentController::class, 'deleteGroup']);
    Route::put('payments/{payment}/general', [PaymentController::class, 'updateGeneral']);
});
