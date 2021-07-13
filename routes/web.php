<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

<<<<<<< HEAD
//Route::get('/', function () {
//    return view('welcome');
//});
=======
Route::get('/', function () {
    return view('welcome');
});
//Auth
Route::get('/login', 'AccountController@showlogin');
Route::post('/login', 'AccountController@login')->name('login');
Route::get('/verify/{userId}', 'AccountController@checkAuth');
Route::post('/update', 'AccountController@update')->name('update');
Route::post('/logout', 'AccountController@userLogout')->name('logout');
>>>>>>> 'first'
