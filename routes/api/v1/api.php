<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
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

JsonApi::register('v1')->routes(function($api) {
    $api->resource('movies')->relationships(function($api) {
        $api->hasMany('likes')->except('replace');
    });

    $api->resource('likes')->except('update');

    $api->resource('purchases')->except('update', 'delete')->relationships(function($api) {
        $api->hasOne('movies')->except('replace');
        $api->hasOne('users')->except('replace');
    });
});

//Route::prefix('v1')->group(function() {
//
//    Route::apiResource('rent', App\Http\Controllers\Api\RentController::class);
//
//    Route::apiResource('penalty', App\Http\Controllers\Api\PenaltyController::class);
//
//    Route::apiResource('purchase', App\Http\Controllers\Api\PurchaseController::class);
//
//    Route::apiResource('like', App\Http\Controllers\Api\LikeController::class);
//
//    Route::apiResource('role', App\Http\Controllers\Api\RoleController::class);
//
//});
