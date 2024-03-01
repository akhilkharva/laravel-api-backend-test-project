<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'api\v1', 'middleware' => 'throttle:25,1'], function () {
    Route::post('login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'posts'], function () {
            Route::get('', [App\Http\Controllers\Api\V1\PostController::class, 'index']);
            Route::get('{id}', [App\Http\Controllers\Api\V1\PostController::class, 'index']);
            Route::post('store/new', [App\Http\Controllers\Api\V1\PostController::class, 'store']);
            Route::post('update/{id}', [App\Http\Controllers\Api\V1\PostController::class, 'store']);
            Route::delete('{id}', [App\Http\Controllers\Api\V1\PostController::class, 'destroy']);
        });
    });
});
