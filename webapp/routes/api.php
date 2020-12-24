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

Route::get('/products', 'ProductController@index')->name('products');
Route::get('/products/{product}', 'ProductController@view')->name('product');

Route::middleware('guest')->group(function() {
    Route::post('/login', 'Auth\AuthController@login')->name('login');
    Route::post('/register', 'Auth\AuthController@register')->name('register');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // FIXME: only a manager can access these
    Route::delete('/products/{product}', 'ProductController@delete')->name('delete-product');
    Route::post('/products/{product}', 'ProductController@put')->name('put-product');
    Route::post('/products', 'ProductController@post')->name('post-product');

    Route::get('/user', 'UserController@index');

    Route::post('/logout', 'Auth\AuthController@logout')->name('logout');
});
