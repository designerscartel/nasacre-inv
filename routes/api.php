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

Route::get('/database', [\App\Http\Controllers\Api\DatabaseController::class, 'index'])->name('api.database');
Route::get('/regions', [\App\Http\Controllers\Api\RegionController::class, 'index'])->name('api.region');
Route::get('/sacres/{sacre}/files/{file}', [\App\Http\Controllers\Api\FileController::class, 'show'])->name('api.file');



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
