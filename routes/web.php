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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

Route::view('login',function (){
    return 'hh';
});

Route::get('/home', 'HomeController@index')->name('home');


Route::get('firebase', 'Firebase\FirebaseController@index');
Route::get('getData', 'Firebase\FirebaseController@getData');

Route::get('create', 'Firebase\FirebaseController@create');
Route::post('fire', 'Firebase\FirebaseController@store')->name('firebase.store');


Route::namespace('Firebase')->prefix('system')->name('admin.')->middleware('auth')
    ->group(function () {
    Route::resource('student','');
});
