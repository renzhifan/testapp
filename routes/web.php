<?php

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

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/auth', 'Auth\LoginController@oauth');

Route::get('/auth/callback', 'Auth\LoginController@callback');

Route::get('/auth/client', 'Auth\LoginController@client');

Route::get('/login', function () {
    return cas()->authenticate();
});

Route::middleware('cas.auth')->get('/logout', function () {
    cas()->logout();
});

Route::middleware('cas.auth')->get('/user', function () {
    return cas()->user();
});