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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => '/admin', 'namespace' => '\App\Http\Controllers\Auth'], function () {
    Route::get('/login', 'LoginController@login');
    Route::post('/login', 'LoginController@postLogin');
    Route::get('/logout', 'LoginController@logout');
});

Route::get('login', '\App\Http\Controllers\FrontEnd\LoginController@login');
Route::post('login', '\App\Http\Controllers\FrontEnd\LoginController@postLogin');
Route::get('register', '\App\Http\Controllers\FrontEnd\LoginController@register');
Route::post('register', '\App\Http\Controllers\FrontEnd\LoginController@postRegister');
Route::get('logout', '\App\Http\Controllers\FrontEnd\LoginController@logout');
Route::get('forgot-password', '\App\Http\Controllers\FrontEnd\LoginController@forgotPassword');
Route::post('forgot-password', '\App\Http\Controllers\FrontEnd\LoginController@forgotPassword');

Route::group(['middleware' => 'checkUserLogin', 'namespace' => 'FrontEnd'], function () {
    // Route front end has required login
});

/**
 * Route admin panel
 * Middelware
 */
Route::group(['middleware' => 'checkAdminLogin', 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('/', 'DashBoardController@index');

    Route::group(['prefix' => 'widgets'], function () {
        Route::get('/index', 'WidgetController@index');
        Route::post('{id}/update', 'WidgetController@update');
        Route::get('{id}/delete', 'WidgetController@delete');
        Route::post('create','WidgetController@create');
    });

    Route::group(['prefix' => 'paygates'], function () {
        Route::get('index', 'PaygateController@index');
        Route::get('{id}/edit', 'PaygateController@edit');
        Route::post('{id}/update', 'PaygateController@update');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('index', 'UserController@index');
        Route::get('{id}/edit', 'UserController@edit');
        Route::post('{id}/update', 'UserController@update');
        Route::get('{id}/delete', 'UserController@delete');
    });

    Route::group(['prefix' => 'menus'], function () {
        Route::get('index', 'MenuController@index');
        Route::get('{id}/edit', 'MenuController@edit');
        Route::post('{id}/update', 'MenuController@update');
        Route::post('create', 'MenuController@store');
        Route::get('show/{id}', 'MenuController@show');
        Route::get('create', 'MenuController@create');
        Route::get('{id}/delete', 'MenuController@destroy');
        Route::get('add', 'MenuController@add');
    });

    Route::group(['prefix' => 'ngan-luong'], function () {
        Route::get('direct-payment', 'DashBoardController@doDirectPayment');
        Route::any('success', 'DashBoardController@success');
    });

    Route::group(['prefix' => 'VNPAY'], function () {
        Route::get('direct-payment', 'DashBoardController@doDirectPayment');
    });

    Route::group(['prefix' => 'Paypal'], function () {
        Route::get('direct-payment', 'DashBoardController@paypalDirectPayment');
    });
});
