<?php

use App\Http\Middleware\ApiAuthMiddleware;
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

Route::get('/welcome', function () {
    return view('welcome');
});

//Test Routes
Route::get('/test-orm','App\Http\Controllers\TestController@testORM');
Route::get('/test-user','App\Http\Controllers\UserController@test');
Route::get('/test-resume','App\Http\Controllers\ResumeController@test');
Route::get('/test-project','App\Http\Controllers\ProjectController@test');
Route::get('/test-tool','App\Http\Controllers\ToolController@test');

//User Routes
Route::post('/api/register','App\Http\Controllers\UserController@register');
Route::post('/api/login','App\Http\Controllers\UserController@login');
Route::put('/api/user/update' ,'App\Http\Controllers\UserController@update');
Route::post('/api/user/upload' ,'App\Http\Controllers\UserController@uploadFile')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{fileName}' ,'App\Http\Controllers\UserController@getImage');
Route::get('/api/user/detail/{id}' ,'App\Http\Controllers\UserController@detail');