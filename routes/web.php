<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {

    Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

        Route::get('/dashboard', [\App\Http\Controllers\DashbordController::class, 'index'])->name('dashboard');

        // Sacre
        Route::post('/sacres', [\App\Http\Controllers\SacreController::class, 'store'])->name('sacres.create');
        Route::get('/sacres/create', [\App\Http\Controllers\SacreController::class, 'create'])->name('sacres.create');
        Route::get('/sacres/{sacre}', [\App\Http\Controllers\SacreController::class, 'show'])->name('sacres.show');
        Route::put('/sacres/{sacre}', [\App\Http\Controllers\SacreController::class, 'update'])->name('sacres.update');


    });
});