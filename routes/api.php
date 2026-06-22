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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/regions', 'CustomerController@regions');
Route::get('/regions/{region}/provinces', 'CustomerController@provinces');
Route::get('/regions/{region}/cities-municipalities', 'CustomerController@regionCities');
Route::get('/provinces/{province}/cities', 'CustomerController@cities');
Route::get('/cities/{city}/barangays', 'CustomerController@barangays');
