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
Route::get('/', function () {
    return response()->json([
        'available_endpoints' => [
            'Get all available endpoints' => route('api'),
            'Get all available checks' => route('api.checks'),
            'Get a specific check (see all available_checks)' => route('api.checks.type', ['server_creation_time']),
            'Get all available providers' => route('api.providers'),
            'Get all available logs for a specific provider' => route('api.logs', 1),
        ],
    ]);
})->name('api');
Route::get('checks', 'Api\ChecksController@index')->name('api.checks');
Route::get('checks/{check}', 'Api\ChecksController@check')->name('api.checks.type');
Route::get('_checks/{check}', 'Api\ChecksController@checksForCharts')->name('api.checks.type.for.charts');
Route::get('providers', 'Api\ProvidersController@index')->name('api.providers');
Route::get('providers/{provider}/logs', 'Api\ProvidersController@logs')->name('api.logs');