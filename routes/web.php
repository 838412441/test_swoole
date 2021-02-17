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
Route::get('/', function () {
    dd("请输入正确的访问路径");
});
Route::group(['namespace' => 'Api', 'prefix' => 'swoole'], function () {
    Route::get('login', 'IndexController@login');
    Route::get('login-pd/{token}', 'IndexController@loginPd');
    Route::get('chat', 'SwooleController@chat');
    Route::get('user-list/{token}', 'SwooleController@userList');
});
