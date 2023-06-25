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
Route::get('تسجيل-الدخول', 'AuthController@login')->name('login');
Route::post('login', 'AuthController@check')->name('login.check');
Route::get('logout', 'AuthController@logout')->name('logout');


Route::prefix('dashboard')->middleware('verifyUser')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/الإعدادات', 'Admincontroller@settings')->name('admin.settings');
    Route::post('/settings', 'Admincontroller@updateSettings')->name('admin.settings.update');
//    Route::get('home', 'DashboardController@index')->name('home');
    Route::post('uploadStudentFile', 'AdminController@exportStudentsExcel')->name('exportStudents');
    Route::post('uploadTeachersFile', 'AdminController@exportTeachersExcel')->name('exportTeachers');
    Route::post('storeStudent', 'AdminController@storeStudent')->name('student.store');
    Route::post('storeGroupMembers', 'StudentController@storeGroupMembers')->name('group.members.store');
    Route::post('storeGroupSupervisor', 'StudentController@storeGroupSupervisor')->name('group.supervisor.store');
    Route::post('acceptTeamJoinRequest', 'StudentController@acceptTeamJoinRequest')->name('acceptTeamJoinRequest');
    // Route::resource('group', 'StudentController')->except(['destroy']);
    Route::post('replyJoinGroupRequest', 'SupervisorController@replayToBeSupervisorRequest')->name('group.supervisor.replyRequest');
});

//Route::view('test', 'test');
//Route::get('test', function () {
//    teacherGenerator(10);
//    return 'done';
//})->name('test');
 Route::get('test','AdminController@test');
