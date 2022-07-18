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
Route::post('/register','App\Http\Controllers\UserController@register');