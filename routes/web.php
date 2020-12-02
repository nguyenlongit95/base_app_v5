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

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
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

    Route::group(['prefix' => 'ngan-luong'], function () {
        Route::get('direct-payment', 'DashBoardController@doDirectPayment');
        Route::any('success', 'DashBoardController@success');
    });

    Route::group(['prefix' => 'VNPAY'], function () {
        Route::get('direct-payment', 'DashBoardController@doDirectPayment');
    });
});
