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

Route::get('create', 'MainController@create');

//Route::get('send', 'EmailController@mail');
//Route::get('viewMail', 'EmailController@show');
Route::get('user/create/password/{token}', 'PasswordController@create');
Route::post('user/store/password', 'PasswordController@store')->name('store.user.password');

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

    Route::namespace('Admin')->prefix('admin')->group(function () {
        Route::get('/', 'AdminController@index')->name('admin.index');
        Route::get('الإعدادات', 'AdminController@settings')->name('admin.settings');
        Route::post('settings', 'AdminController@updateSettings')->name('admin.settings.update');
        Route::post('uploadExcelFile', 'AdminController@exportExcelFile')->name('admin.exportExcelFile');
//        Route::post('uploadTeachersFile', 'AdminController@exportTeachersExcel')->name('exportTeachers');
        Route::post('user/store', 'AdminController@storeUser')->name('user.store');
        Route::get('students', 'StudentController@index')->name('admin.student.index');
        Route::get('student/edit/{user_id}', 'StudentController@edit')->name('admin.student.edit');
        Route::post('student/update/{key}', 'StudentController@update')->name('admin.student.update');
        Route::get('student/destroy/{user_id}', 'StudentController@destroy')->name('admin.student.destroy');
        Route::get('teachers', 'TeacherController@index')->name('admin.teacher.index');
        Route::get('teacher/show/{key}', 'TeacherController@show')->name('admin.teacher.show');
        Route::get('teacher/edit/{key}', 'TeacherController@edit')->name('admin.teacher.edit');
        Route::post('teacher/update/{key}', 'TeacherController@update')->name('admin.teacher.update');
        Route::get('teacher/promotion/{key}', 'TeacherController@promotion')->name('admin.teacher.promotion');
        Route::get('teacher/destroy/{key}', 'TeacherController@destroy')->name('admin.teacher.destroy');
        Route::get('groups', 'GroupController@index')->name('admin.group.index');
        Route::get('groups/edit/{group_key}', 'GroupController@edit')->name('admin.group.edit');
        Route::post('groups/update/{group_key}', 'GroupController@update')->name('admin.group.update');
        Route::post('replyJoinGroupRequest', 'MainController@replayToBeSupervisorRequest')->name('admin.group.replyRequest');
    });

    Route::get('teacher', 'TeacherController@index')->name('teacher.index');
    Route::post('storeGroupMembers', 'StudentController@storeGroupMembers')->name('group.members.store');
    Route::post('storeGroupSupervisor', 'StudentController@storeGroupSupervisor')->name('group.teacher.store');
//    Route::post('acceptTeamJoinRequest', 'StudentController@acceptTeamJoinRequest')->name('acceptTeamJoinRequest');
    Route::post('teacher/replyJoinGroupRequest', 'MainController@replayToBeSupervisorRequest')->name('teacher.group.replyRequest');
});
