<?php

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

Route::post('login', 'AuthController@login');
Route::group(['middleware' => ['auth:api','EnsureTokenIsValid', 'throttle:10,1']], function() {
    Route::post('verify/{user}', 'AuthController@authUser');
    Route::post('update/{id}', 'AuthController@updateUser');
    Route::post('logout', 'AuthController@updateUser');
});
