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

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('firebase', 'FirebaseController@store');


Route::get('login', 'AuthController@login')->name('login');
Route::post('login', 'AuthController@check')->name('login.check');
Route::get('logout', 'AuthController@logout')->name('logout');


Route::prefix('dashboard')->middleware('verifyUser')->group(function () {
    Route::get('/', 'AdminController@index')->name('dashboard');
//    Route::get('home', 'AdminController@index')->name('home');
    Route::post('uploadStudentFile', 'AdminController@exportStudentExcel')->name('exportStudents');
    Route::post('storeStudent', 'AdminController@storeStudent')->name('student.store');
});

Route::view('test','test');
