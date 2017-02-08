<?php

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
    return redirect()->route('login');
});

Auth::routes();

Route::get('/register', function () {
    return redirect()->route('login');
});

Route::get('/home', 'HomeController@home');

Route::get('/top5visibility/{requestDate?}', 'Top5visibilityController@home');
Route::get('/top5visibility/{date}/live/data', 'Top5visibilityController@getData');
Route::get('/top5visibility/live/data', 'Top5visibilityController@getData');
Route::get('/top5visibility/{date}/download', 'Top5visibilityController@downloadData');


Route::get('/humidity', 'HumidityController@home');
Route::get('/humidity/stations', 'HumidityController@getStations');
Route::get('/humidity/live/data/{id}', 'HumidityController@getData');
Route::get('/humidity/{id}/download', 'HumidityController@downloadData');

//Route::get('/download', 'HomeController@downloadData');
