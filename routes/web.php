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
use Illuminate\Support\Facades\URL;

if (env('APP_ENV') === 'production')
    URL::forceScheme('https');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('send', 'EmailController@mail');
Route::get('viewMail', 'EmailController@show');
Route::get('student/create/password/{token}', 'Student\PasswordController@create');
Route::post('student/store/password', 'Student\PasswordController@store')->name('store.student.password');

Route::get('تسجيل_الدخول', 'AuthController@login')->name('login');
Route::get('login', 'AuthController@login')->name('login');
Route::post('login', 'AuthController@check')->name('login.check');
Route::get('logout', 'AuthController@logout')->name('logout');

Route::prefix('dashboard')->middleware('verifyUser')->group(function () {

    Route::namespace('Student')->prefix('student')->group(function () {
        Route::get('/', 'DashboardController@index')->name('student.index');
        Route::get('wait', 'DashboardController@wait')->name('student.wait');
        Route::get('createGroup', 'GroupController@create')->name('student.group.create');
        Route::post('createGroup/store', 'GroupController@store')->name('student.group.store');
        Route::post('createGroup/storeExtra', 'GroupController@storeExtra')->name('student.group.storeExtra');
        Route::post('group/response', 'GroupController@memberResponse')->name('student.group.response');
        Route::post('group/addSupervisor', 'GroupController@storeGroupSupervisor')->name('student.group.storeSupervisor');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/', 'AdminController@index')->name('admin.index');
        Route::get('الإعدادات', 'AdminController@settings')->name('admin.settings');
        Route::post('settings', 'AdminController@updateSettings')->name('admin.settings.update');
        Route::post('uploadStudentFile', 'AdminController@exportStudentsExcel')->name('exportStudents');
        Route::post('uploadTeachersFile', 'AdminController@exportTeachersExcel')->name('exportTeachers');
        Route::post('storeStudent', 'AdminController@storeStudent')->name('student.store');
    });

    Route::get('teacher', 'TeacherController@index')->name('teacher.index');


    Route::post('storeGroupMembers', 'StudentController@storeGroupMembers')->name('group.members.store');
    Route::post('storeGroupSupervisor', 'StudentController@storeGroupSupervisor')->name('group.teacher.store');
    Route::post('acceptTeamJoinRequest', 'StudentController@acceptTeamJoinRequest')->name('acceptTeamJoinRequest');
    Route::post('replyJoinGroupRequest', 'TeacherController@replayToBeSupervisorRequest')->name('group.teacher.replyRequest');
});
