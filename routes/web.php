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

Route::get('login', 'AuthController@login')->name('login');
Route::post('login', 'AuthController@check')->name('login.check');
Route::get('logout', 'AuthController@logout')->name('logout');


Route::prefix('dashboard')->middleware('verifyUser')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/الإعدادات', 'Admincontroller@settings')->name('admin.settings');
    Route::post('/الإعدادات', 'Admincontroller@update')->name('admin.settings.update');
//    Route::get('home', 'DashboardController@index')->name('home');
    Route::post('uploadStudentFile', 'DashboardController@exportStudentsExcel')->name('exportStudents');
    Route::post('uploadTeachersFile', 'DashboardController@exportTeachersExcel')->name('exportTeachers');
    Route::post('storeStudent', 'DashboardController@storeStudent')->name('student.store');
    Route::post('storeGroupMembers', 'StudentController@storeGroupMembers')->name('group.members.store');
    Route::post('storeGroupSupervisor', 'StudentController@storeGroupSupervisor')->name('group.supervisor.store');
    Route::post('acceptTeamJoinRequest', 'StudentController@acceptTeamJoinRequest')->name('acceptTeamJoinRequest');
    // Route::resource('group', 'StudentController')->except(['destroy']);
    Route::post('replyJoinGroupRequest', 'SupervisorController@replayToBeSupervisorRequest')->name('group.supervisor.replyRequest');
});

//Route::view('test', 'test');
Route::get('test', function () {
    teacherGenerator(10);
    return 'done';
})->name('test');
