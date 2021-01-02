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

// Manager routes
Route::middleware('auth:sanctum', 'user-manager')->group(function() {
    // users routes
    Route::get('/users', 'UserController@list');
    Route::post('/users', 'UserController@create');
    Route::get('/users/{user}', 'UserController@view');
    Route::put('/users/{user}', 'UserController@update');
    Route::delete('/users/{user}', 'UserController@delete');

    // photo upload route
    Route::post('/users/{user}/photo', 'UserController@photo');
    Route::delete('/users/{user}/photo', 'UserController@photoDelete');

    // block/unblock a user route
    Route::put('/users/{user}/block', 'UserController@block');

    // products routes
    Route::delete('/products/{product}', 'ProductController@delete')->name('delete-product');
    Route::post('/products/{product}', 'ProductController@put')->name('put-product');
    Route::post('/products', 'ProductController@post')->name('post-product');

    Route::get('/orders', 'OrderController@listAll');
    Route::post('/orders/{order}/cancel', 'OrderController@cancel');
});

Route::get('/test', 'OrderController@test');

Route::middleware('auth:sanctum', 'user-cook')->group(function() {
    Route::get('/cook/orders', 'OrderController@listCook');
    Route::post('/cook/orders/{order}/prepare', 'OrderController@prepare');
});

Route::middleware('auth:sanctum', 'user-deliveryman')->group(function() {
    Route::get('/deliveryman/orders', 'OrderController@listDeliverman');
    Route::post('/deliveryman/orders/{order}/pickup', 'OrderController@pickup');
    Route::post('/deliveryman/orders/{order}/deliver', 'OrderController@deliver');
});

Route::middleware('auth:sanctum', 'user-customer')->group(function () {
    Route::post('/customer/orders', 'OrderController@create');
    Route::get('/customer/orders', 'OrderController@listCustomer');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // get order
    Route::get('/orders/{order}', 'OrderController@view');

    // get current user info
    Route::get('/user', 'UserController@profile');
    Route::put('/user', 'UserController@updateProfile');
    Route::post('/user/photo', 'UserController@photoProfile');
    Route::delete('/user/photo', 'UserController@photoDeleteProfile');

    Route::post('/logout', 'Auth\AuthController@logout')->name('logout');
});
