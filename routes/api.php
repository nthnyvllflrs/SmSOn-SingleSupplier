<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['json.response']], function () {

    // public routes
    Route::post('/login', 'AuthController@login');

    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'AuthController@logout');
        
        Route::get('/user', function (Request $request) { return $request->user();}); // Return current logged in user
        Route::get('/notifications', function (Request $request) {
            return $request->user()->notifications;
        });

        Route::group(['prefix' => '/administrator'], function($request) {
            Route::get('/{administrator}', 'AuthController@show');
            Route::put('/{administrator}', 'AuthController@update');
        });

        Route::group(['prefix' => '/supplier'], function($request) {
            Route::post('/', 'SupplierController@store');
            Route::get('/', 'SupplierController@index');
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
    
        Route::group(['prefix' => '/product'], function($request) {
            Route::get('/', 'ProductController@index');
            Route::post('/', 'ProductController@store');
            Route::get('/{product}', 'ProductController@show');
            Route::put('/{product}', 'ProductController@update');
            Route::delete('/{product}', 'ProductController@destroy');
        });

        Route::group(['prefix' => '/logistic'], function($request) {
            Route::get('/', 'LogisticController@index');
            Route::post('/', 'LogisticController@store');
            Route::get('/{logistic}', 'LogisticController@show');
            Route::put('/{logistic}', 'LogisticController@update');
            Route::delete('/{logistic}', 'LogisticController@destroy');
        });

        Route::group(['prefix' => 'order-request'], function($request) {
            Route::post('/', 'OrderRequestController@store');
            Route::get('/', 'OrderRequestController@index');
            Route::get('/receivables', 'OrderRequestController@receivables');
            Route::get('/{order_request}', 'OrderRequestController@show');
            Route::put('/{order_request}/status', 'OrderRequestController@update_status');
            Route::delete('/{order_request}', 'OrderRequestController@destroy');
            
        });

        Route::group(['prefix' => 'manifest'], function($request) {
            Route::post('/', 'ManifestController@store');
            Route::get('/', 'ManifestController@index');
            Route::get('/logistics', 'ManifestController@supplier_logistics');
            Route::get('/order-requests', 'ManifestController@supplier_order_requests');
            Route::get('/{manifest}/edit', 'ManifestController@edit');
            Route::put('/{manifest}', 'ManifestController@update');
            Route::delete('/{manifest}', 'ManifestController@destroy');
        });
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