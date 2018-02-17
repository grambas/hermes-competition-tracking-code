<?php

Route::group(['prefix' => '/api/v1', 'as' => 'api.'], function () {
    //DB
        Route::get('parcel/status/get', 'MainController@getParcelStatus');
        Route::post('parcel/status/add', 'MainController@addParcelStatus');
        Route::post('parcel/create', 'MainController@createParcel');

    //NOT DB    
        Route::get('parcel/tracking/compress', 'MainController@compressTracking');
        Route::get('parcel/tracking/reverse', 'MainController@reverseTracking');
});


Route::get('/', 'MainController@homeView')->name('home');
Route::get('track', 'MainController@trackView')->name('track');
Route::get('simulation', 'MainController@simulationView')->name('simulation');
Route::get('demosntration', 'MainController@demosntrationView')->name('demosntration');
