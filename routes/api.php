<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookController;
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


Route::post('register', 'App\Http\Controllers\API\AuthController@register');
Route::post('login', 'App\Http\Controllers\API\AuthController@login');

Route::get('all-books', 'App\Http\Controllers\API\BookController@allBooks');
Route::middleware('auth:api')->group( function () {
    Route::resource('book', BookController::class);
    Route::get('authors', 'App\Http\Controllers\API\AuthController@getAuthors');
    Route::post('changeUserStatus', 'App\Http\Controllers\API\AuthController@changeUserStatus');
    Route::post('logout', 'App\Http\Controllers\API\AuthController@logout');
});