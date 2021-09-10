<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// 首页
Route::get('/','StaticPagesController@home')->name('home');

// 帮助页
Route::get('/help','StaticPagesController@help')->name('help');

// 关于我们
Route::get('/about','StaticPagesController@about')->name('about');

// 注册
Route::get('/signup','UsersController@create')->name('signup');

// 用户资源路由
Route::resource('/users','UsersController');
// Route::get('/users', 'UsersController@index')->name('users.index');
// Route::get('/users/create', 'UsersController@create')->name('users.create');
// Route::get('/users/{user}', 'UsersController@show')->name('users.show');
// Route::post('/users', 'UsersController@store')->name('users.store');
// Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');
// Route::patch('/users/{user}', 'UsersController@update')->name('users.update');
// Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');

// 用户会话页面
Route::get('/login', 'SessionsController@create')->name('login');

// 接受用户会话数据
Route::post('/login', 'SessionsController@store')->name('login');

// 销毁用户会话
Route::delete('/login', 'SessionsController@destroy')->name('logout');

// 用户账户激活路由
Route::get('/signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 重置密码 填写邮箱页面
Route::get('/password/reset', 'PasswordController@showLinkRequestForm')->name('password.request');

// 重置密码 接受邮箱数据
Route::post('/password/email', 'PasswordController@sendResetLinkEmail')->name('password.email');

// 重置密码 填写新密码页面
Route::get('/password/reset/{token}', 'PasswordController@showResetForm')->name('password.reset');

// 重置密码 接收新密码数据
Route::post('/password/reset', 'PasswordController@reset')->name('password.update');

// 发布微博
Route::resource('/statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

// 粉丝列表
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');

// 关注用户列表
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');

// 关注用户
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
// 取消关注
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');

