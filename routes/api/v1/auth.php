<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordLinkController;
use App\Http\Controllers\Api\Auth\ShowUserController;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

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
