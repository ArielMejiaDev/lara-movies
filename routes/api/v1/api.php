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
    $api->resource('movies');
});

Route::prefix('v1')->group(function() {

//    Route::apiResource('movies', App\Http\Controllers\Api\MovieController::class, [
//        'names' => [
//            'index' => 'api:v1:movies.index',
//            'show' => 'api:v1:movies.show',
//        ]
//    ]);

    Route::apiResource('rent', App\Http\Controllers\Api\RentController::class);

    Route::apiResource('penalty', App\Http\Controllers\Api\PenaltyController::class);

    Route::apiResource('purchase', App\Http\Controllers\Api\PurchaseController::class);

    Route::apiResource('like', App\Http\Controllers\Api\LikeController::class);

    Route::apiResource('role', App\Http\Controllers\Api\RoleController::class);

});
