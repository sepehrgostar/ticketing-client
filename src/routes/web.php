<?php

Route::group(['middleware' => ['web','auth'], 'as' => 'sepehrgostar.LaravelClient.', 'prefix' => 'gandom', 'namespace' => 'Sepehrgostar\LaravelClient\Http\Controllers'], function () {

    Route::get('test/', 'main@index')->name('main.index');

});
