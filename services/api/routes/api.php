<?php

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('')
    ->group(function () {
        Route::any('ping', fn() => response()->json(['pong' => 1]));

        Route::prefix('weather')
            ->name('weather.')
            ->group(function () {
                Route::post('', [WeatherController::class, 'fetch'])->name('fetch');
                Route::post('stations', [WeatherController::class, 'getWeather'])->name('stations.weather');
                Route::get('stations', [WeatherController::class, 'stations'])->name('stations');
            });

        Route::prefix('track')
            ->name('track.')
            ->group(function () {
                Route::post('', [TrackingController::class, 'track'])->name('store');
                Route::get('search', [TrackingController::class, 'search'])->name('search');
            });
    });
