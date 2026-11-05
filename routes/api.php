<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/database/{sacre}/shared/{file}', [\App\Http\Controllers\Api\SharedController::class, 'show'])->name('api.shared-file');
Route::get('/database/{sacre}/files/{file}', [\App\Http\Controllers\Api\FileController::class, 'show'])->name('api.file');
Route::get('/database/{term}', [\App\Http\Controllers\Api\DatabaseController::class, 'show'])->name('api.search');
Route::get('/database', [\App\Http\Controllers\Api\DatabaseController::class, 'index'])->name('api.database');
Route::prefix('re-hubs')->name('api.re-hubs.')->group(function () {
    Route::get('/regions/places-of-interest', [\App\Http\Controllers\Api\ReHubController::class, 'placeRegions'])->name('regions.places-of-interest');
    Route::get('/regions/school-speakers', [\App\Http\Controllers\Api\ReHubController::class, 'speakerRegions'])->name('regions.school-speakers');
    Route::get('/places-of-interest', [\App\Http\Controllers\Api\ReHubController::class, 'places'])->name('places-of-interest');
    Route::get('/school-speakers', [\App\Http\Controllers\Api\ReHubController::class, 'speakers'])->name('school-speakers');
});

Route::get('/regions', [\App\Http\Controllers\Api\ReHubController::class, 'legacyRegions'])->name('api.region');
Route::get('/regions/{region}/places', [\App\Http\Controllers\Api\ReHubController::class, 'legacyRegionPlaces'])->name('api.region.places');

Route::get('/shared/{term}', [\App\Http\Controllers\Api\SharedController::class, 'shared'])->name('api.shared.search');
Route::get('/shared', [\App\Http\Controllers\Api\SharedController::class, 'index'])->name('api.shared');

Route::post('/booking', [\App\Http\Controllers\Api\BookingController::class, 'store'])->name('api.booking');



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
