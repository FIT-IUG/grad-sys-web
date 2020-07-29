<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a admin which
| contains the "web" middleware admin. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

if (env('APP_ENV') === 'production')
    URL::forceScheme('https');


Route::get('test', 'MainController@test');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('create', 'MainController@create');

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
        Route::get('createGroup', 'GroupController@create')->name('student.admin.create');
        Route::post('createGroup/store', 'GroupController@store')->name('student.admin.store');
        Route::post('createGroup/storeExtra', 'GroupController@storeExtra')->name('student.admin.storeExtra');
        Route::post('admin/response', 'GroupController@memberResponse')->name('student.admin.response');
        Route::post('admin/addSupervisor', 'GroupController@storeGroupSupervisor')->name('student.admin.storeSupervisor');
    });

    Route::namespace('Admin')->prefix('admin')->group(function () {
        Route::get('/', 'DashboardController@index')->name('admin.index');
        Route::get('الإعدادات', 'DashboardController@settings')->name('admin.settings');
        Route::post('settings', 'DashboardController@updateSettings')->name('admin.settings.update');
        Route::post('tag/store', 'TagController@store')->name('admin.tag.store');
        Route::get('tag/edit/{tag_key}', 'TagController@edit')->name('admin.tag.edit');
        Route::post('tag/update', 'TagController@update')->name('admin.tag.update');
        Route::get('tag/destroy/{tag_key}', 'TagController@destroy')->name('admin.tag.destroy');
        Route::post('uploadExcelFile', 'DashboardController@exportExcelFile')->name('admin.exportExcelFile');
        Route::post('user/store', 'DashboardController@storeUser')->name('user.store');
        Route::get('students', 'StudentController@index')->name('admin.student.index');
        Route::get('student/edit/{user_id}', 'StudentController@edit')->name('admin.student.edit');
        Route::post('student/update/{key}', 'StudentController@update')->name('admin.student.update');
        Route::get('student/destroy/{user_id}', 'StudentController@destroy')->name('admin.student.destroy');
        Route::get('teachers', 'TeacherController@index')->name('admin.teacher.index');
        Route::get('teacher/show/{key}', 'TeacherController@show')->name('admin.teacher.show');
        Route::get('teacher/edit/{key}', 'TeacherController@edit')->name('admin.teacher.edit');
        Route::post('teacher/update/{key}', 'TeacherController@update')->name('admin.teacher.update');
        Route::get('teacher/promotion/{key}', 'TeacherController@promotion')->name('admin.teacher.promotion');
        Route::get('teacher/demotion/{key}', 'TeacherController@demotion')->name('admin.teacher.demotion');
        Route::get('teacher/destroy/{key}', 'TeacherController@destroy')->name('admin.teacher.destroy');
        Route::get('groups', 'GroupController@index')->name('admin.admin.index');
        Route::get('groups/edit/{group_key}', 'GroupController@edit')->name('admin.admin.edit');
        Route::post('groups/update/{group_key}', 'GroupController@update')->name('admin.admin.update');
        Route::post('groups/update/teacher/{group_key}', 'GroupController@updateTeacher')->name('admin.admin.update.teacher');
        Route::post('replyJoinGroupRequest', 'DashboardController@replayToBeSupervisor')->name('admin.admin.replyRequest');
    });

    Route::get('teacher', 'TeacherController@index')->name('teacher.index');
    Route::post('storeGroupMembers', 'StudentController@storeGroupMembers')->name('admin.members.store');
    Route::post('storeGroupSupervisor', 'StudentController@storeGroupSupervisor')->name('admin.teacher.store');
//    Route::post('acceptTeamJoinRequest', 'StudentController@acceptTeamJoinRequest')->name('acceptTeamJoinRequest');
    Route::post('teacher/replyJoinGroupRequest', 'MainController@replayToBeSupervisorRequest')->name('teacher.admin.replyRequest');
});
