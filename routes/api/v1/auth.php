<?php

use App\Http\Controllers\Api\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordLinkController;
use App\Http\Controllers\Api\Auth\ShowUserController;
use App\Http\Controllers\Api\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Authentication Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API authentication routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', LoginController::class)
    ->name('login');

Route::get('/user', ShowUserController::class)
    ->name('user.show')
    ->middleware('auth:api');

Route::post('/register', RegisterController::class)
    ->name('register');

Route::post('/forgot-password', ForgotPasswordController::class)
    ->name('forgot-password');

Route::post('/reset', ResetPasswordController::class)
    ->name('reset-password');

Route::get('/reset/{token}', ResetPasswordLinkController::class)
    ->name('reset-password-link');

Route::get('/logout', LogoutController::class)
    ->name('logout');

Route::get('/verify-email', EmailVerificationPromptController::class)->middleware('auth:api')->name('verification.notice');

Route::get('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');


// test route to show that the verified middleware works!
Route::get('/verified-only', function(Request $request){

    return response()->json([
        'message' => $request->user()->name. ' you are verified!',
    ], 200);

})->middleware('auth:api','verified')->name('verified-only');
