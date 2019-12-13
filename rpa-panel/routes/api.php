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

Route::post('/process/cc/downloadImages', 'API\CCController@downloadImages');

Route::post('/process/hfc/start', 'API\HFCController@start');

// Route::middleware('auth:api')->group( function () {
//     Route::get('/downloadImages', 'API\CCController@downloadImages');
// });