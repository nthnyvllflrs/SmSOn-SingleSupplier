<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['json.response']], function () {

    // public routes
    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');

    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'AuthController@logout');

        Route::get('/user', function (Request $request) {
            return $request->user(); // Return current logged in user
        });
    });

    Route::resource('customer', 'CustomerController',
                    array(
                        'only' => array(
                            'index', 'store', 'show', 'update', 'destroy'
                        )
                    ));

    Route::resource('supplier', 'SupplierController',
                    array(
                        'only' => array(
                            'index', 'store', 'show', 'update', 'destroy'
                        )
                    ));

    Route::resource('logistic', 'LogisticController',
                    array(
                        'only' => array(
                            'index', 'store', 'show', 'update', 'destroy'
                        )
                    ));
});