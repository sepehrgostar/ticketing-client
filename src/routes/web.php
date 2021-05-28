<?php

Route::group(['middleware' => ['web','auth'], 'as' => 'sepehrgostar.api.', 'prefix' => 'gandom', 'namespace' => 'Sepehrgostar\Api\Http\Controllers'], function () {

    Route::get('test/', 'main@index')->name('main.index');

});
