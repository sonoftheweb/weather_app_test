<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['sanitize']], function () {
    Route::delete('erase', 'WeatherController@erase'); // this handles erase
    Route::get('weather/{locOrTemp}', 'WeatherController@locOrTemp'); // this handles both location and temp filter

    Route::delete('weather/{id}', 'WeatherController@destroy');
    Route::post('weather/{id}', 'WeatherController@update');

    Route::resource('weather', 'WeatherController')->only([
        'index', 'store'
    ]);
});
