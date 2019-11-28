<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['json.response']], function () {

    // public routes
    Route::post('/login', 'AuthController@login');

    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'AuthController@logout');

        Route::get('/user', function (Request $request) {
            return $request->user(); // Return current logged in user
        });
    });

    Route::group(['prefix' => '/supplier'], function($request) {
        Route::get('/', 'SupplierController@index');
        Route::post('/', 'SupplierController@store');
        Route::get('/{supplier}', 'SupplierController@show');
        Route::put('/{supplier}', 'SupplierController@update');
        Route::delete('/{supplier}', 'SupplierController@destroy');
    });

    Route::group(['prefix' => '/customer'], function($request) {
        Route::get('/', 'CustomerController@index');
        Route::post('/', 'CustomerController@store');
        Route::get('/{customer}', 'CustomerController@show');
        Route::put('/{customer}', 'CustomerController@update');
        Route::delete('/{customer}', 'CustomerController@destroy');
    });

    // Route::resource('customer', 'CustomerController',
    //                 array(
    //                     'only' => array(
    //                         'index', 'store', 'show', 'update', 'destroy'
    //                     )
    //                 ));

    // Route::resource('supplier', 'SupplierController',
    //                 array(
    //                     'only' => array(
    //                         'index', 'store', 'show', 'update', 'destroy'
    //                     )
    //                 ));

    // Route::resource('logistic', 'LogisticController',
    //                 array(
    //                     'only' => array(
    //                         'index', 'store', 'show', 'update', 'destroy'
    //                     )
    //                 ));

    // Route::resource('product', 'ProductController',
    //                 array(
    //                     'only' => array(
    //                         'index', 'store', 'show', 'update', 'destroy'
    //                     )
    //                 ));
});