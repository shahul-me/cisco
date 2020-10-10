<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::POST('/login', 'Api\UserController@login');
Route::POST('/create', 'Api\RouterController@create');
Route::get('/list-ip', 'Api\RouterController@ListByIp');
Route::POST('/update-ip', 'Api\RouterController@updateByIp');
Route::get('/list-type', 'Api\RouterController@listByType');
Route::delete('/delete', 'Api\RouterController@delete');

