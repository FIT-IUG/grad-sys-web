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

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('تسجيل-الدخول', 'AuthController@login')->name('login');
Route::get('login', 'AuthController@login')->name('login');
Route::post('login', 'AuthController@check')->name('login.check');
Route::get('logout', 'AuthController@logout')->name('logout');

Route::prefix('dashboard')->middleware('verifyUser')->group(function () {

    Route::namespace('Student')->group(function () {
//        Route::resource('student', 'DashboardController');
        Route::get('/student','DashboardController@index')->name('student.index');
        Route::get('/student/wait', 'DashboardController@wait')->name('student.wait');
        Route::get('/student/createGroup', 'GroupController@create')->name('student.group.create');
        Route::post('/student/createGroup/store', 'GroupController@store')->name('student.group.store');
        Route::post('/student/createGroup/storeExtra', 'GroupController@storeExtra')->name('student.group.storeExtra');
        Route::post('/student/group/response', 'GroupController@memberResponse')->name('student.group.response');
        Route::post('/student/group/addSupervisor', 'GroupController@storeGroupSupervisor')->name('student.group.storeSupervisor');
    });

    Route::get('teacher','TeacherController@index')->name('teacher.index');

    Route::get('/admin', 'AdminController@index')->name('admin.index');
//    Route::get('/', 'DashboardController@student')->name('dashboard.student');
    Route::get('/الإعدادات', 'AdminController@settings')->name('admin.settings');
    Route::post('/settings', 'AdminController@updateSettings')->name('admin.settings.update');
    Route::post('uploadStudentFile', 'AdminController@exportStudentsExcel')->name('exportStudents');
    Route::post('uploadTeachersFile', 'AdminController@exportTeachersExcel')->name('exportTeachers');
    Route::post('storeStudent', 'AdminController@storeStudent')->name('student.store');
    Route::post('storeGroupMembers', 'StudentController@storeGroupMembers')->name('group.members.store');
    Route::post('storeGroupSupervisor', 'StudentController@storeGroupSupervisor')->name('group.teacher.store');
    Route::post('acceptTeamJoinRequest', 'StudentController@acceptTeamJoinRequest')->name('acceptTeamJoinRequest');
    Route::post('replyJoinGroupRequest', 'TeacherController@replayToBeSupervisorRequest')->name('group.teacher.replyRequest');
});


//Route::view('test', 'test');
//Route::get('test', function () {
//    teacherGenerator(10);
//    return 'done';
//})->name('test');
Route::get('test', 'AdminController@test');
