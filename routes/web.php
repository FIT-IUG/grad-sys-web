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

use App\Mail\SendCreatePassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

if (env('APP_ENV') === 'production')
    URL::forceScheme('https');


Route::get('test', 'MainController@test');
Route::get('test2', function () {
    Mail::to('samer@example.com')->send(new SendCreatePassword());
    return 'send email successfully';
});

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('create', 'MainController@create');

Route::get('user/create/password/{token}', 'PasswordController@create');
Route::get('user/edit/password/{token}', 'PasswordController@edit');
Route::get('user/update/password/{token}', 'PasswordController@update');
Route::post('user/store/password', 'PasswordController@store')->name('user.password.store');
Route::get('user/restore/password/', 'PasswordController@restore')->name('user.password.restore');
Route::post('user/send/password', 'PasswordController@send')->name('user.password.send');
Route::post('user/update/password', 'PasswordController@update')->name('user.password.update');

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
        Route::get('/', 'DashboardController@index')->name('admin.index');
        Route::get('الإعدادات', 'DashboardController@settings')->name('admin.settings');
        Route::post('settings', 'DashboardController@updateSettings')->name('admin.settings.update');
        Route::post('tag/store', 'TagController@store')->name('admin.tag.store');
        Route::get('tag/edit/{tag_key}', 'TagController@edit')->name('admin.tag.edit');
        Route::post('tag/update', 'TagController@update')->name('admin.tag.update');
        Route::get('tag/destroy/{tag_key}', 'TagController@destroy')->name('admin.tag.destroy');
        Route::post('department/update', 'DepartmentController@update')->name('admin.department.update');
        Route::get('department/destroy/{department_key}', 'DepartmentController@destroy')->name('admin.department.destroy');
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
        Route::get('groups', 'GroupController@index')->name('admin.group.index');
        Route::get('groups/edit/{group_key}', 'GroupController@edit')->name('admin.group.edit');
        Route::post('groups/update/{group_key}', 'GroupController@update')->name('admin.group.update');
        Route::post('groups/update/teacher/{group_key}', 'GroupController@updateTeacher')->name('admin.group.update.teacher');
        Route::post('replyJoinGroupRequest', 'DashboardController@replayToBeSupervisor')->name('admin.group.replyRequest');
    });

    Route::get('teacher', 'TeacherController@index')->name('teacher.index');
    Route::post('storeGroupMembers', 'StudentController@storeGroupMembers')->name('group.members.store');
    Route::post('storeGroupSupervisor', 'StudentController@storeGroupSupervisor')->name('group.teacher.store');
    Route::post('teacher/replyJoinGroupRequest', 'MainController@replayToBeSupervisorRequest')->name('teacher.group.replyRequest');
});
