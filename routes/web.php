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
use App\Mail\StudentMakePasswordMail;


Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email', function () {
    return new StudentMakePasswordMail();
});

Route::namespace('Firebase')->group(function () {

});
Route::get('/students/export', 'Firebase\StudentsController@export');
Route::get('/ex', 'Firebase\StudentsController@import');
Route::get('/export', function () {
    return view('export');
});
Route::post('export', 'Firebase\StudentsController@import')->name('exportStudent');

Route::get('/home', 'HomeController@index')->name('home');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
