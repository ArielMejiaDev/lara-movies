<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API V1 routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

JsonApi::register('v1')->routes(function($api) {
    $api->resource('movies')->relationships(function($api) {
        $api->hasMany('likes')->except('replace');
    });

    $api->resource('likes')->except('update')->relationships(function($api) {
        $api->hasOne('movies')->except('replace');
        $api->hasOne('users')->except('replace');
    });

    $api->resource('purchases')->except('update', 'delete')->relationships(function($api) {
        $api->hasOne('movies')->except('replace');
        $api->hasOne('users')->except('replace');
    });

    $api->resource('rentals')->except('update', 'delete')->relationships(function ($api) {
        $api->hasOne('movies')->except('replace');
        $api->hasOne('users')->except('replace');
    });

    $api->resource('users')->only('index', 'read', 'update');
});
