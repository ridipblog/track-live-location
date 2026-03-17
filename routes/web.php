<?php

use App\Http\Controllers\TrackLocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/track', [TrackLocationController::class, 'index']);
Route::get('/live-location', [TrackLocationController::class, 'liveLocation']);
Route::post('/update-location', [TrackLocationController::class, 'updateLocation']);
Route::get('/map-plot', [TrackLocationController::class, 'mapPlot']);
