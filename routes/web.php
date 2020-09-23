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
        Route::post('/sacres', [\App\Http\Controllers\SacreController::class, 'store'])->name('sacres.store');
        Route::get('/sacres/create', [\App\Http\Controllers\SacreController::class, 'create'])->name('sacres.create');
        Route::get('/sacres/{sacre}', [\App\Http\Controllers\SacreController::class, 'show'])->name('sacres.show');
        Route::put('/sacres/{sacre}', [\App\Http\Controllers\SacreController::class, 'update'])->name('sacres.update');
        Route::delete('/sacres/{sacre}', [\App\Http\Controllers\SacreController::class, 'destroy'])->name('sacres.destroy');
        Route::post('/sacres/{sacre}/contacts', [\App\Http\Controllers\SacreContactController::class, 'store'])->name('sacre-contacts.store');
        Route::put('/sacres/{sacre}/contacts/{contact}', [\App\Http\Controllers\SacreContactController::class, 'update'])->name('sacre-contacts.update');
        Route::delete('/sacres/{sacre}/contacts/{contact}', [\App\Http\Controllers\SacreContactController::class, 'destroy'])->name('sacre-contacts.destroy');

        // Region
        Route::get('/regions', [\App\Http\Controllers\RegionController::class, 'index'])->name('regions.index');
        Route::post('/regions', [\App\Http\Controllers\RegionController::class, 'store'])->name('regions.store');
        Route::get('/regions/create', [\App\Http\Controllers\RegionController::class, 'create'])->name('regions.create');
        Route::get('/regions/{region}', [\App\Http\Controllers\RegionController::class, 'show'])->name('regions.show');
        Route::put('/regions/{region}', [\App\Http\Controllers\RegionController::class, 'update'])->name('regions.update');

        // Campaign
        Route::get('/campaigns', [\App\Http\Controllers\CampaignController::class, 'index'])->name('campaigns.index');
        Route::post('/campaigns', [\App\Http\Controllers\CampaignController::class, 'store'])->name('campaigns.store');
        Route::get('/campaigns/create', [\App\Http\Controllers\CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns/send', [\App\Http\Controllers\CampaignController::class, 'send'])->name('campaigns.send');
        Route::get('/campaigns/{campaign}', [\App\Http\Controllers\CampaignController::class, 'show'])->name('campaigns.show');
        Route::put('/campaigns/{campaign}', [\App\Http\Controllers\CampaignController::class, 'update'])->name('campaigns.update');
        Route::post('/campaigns/{campaign}/send', [\App\Http\Controllers\CampaignController::class, 'send'])->name('campaigns.send');
        
        // CampaignGroups
        Route::get('/groups', [\App\Http\Controllers\CampaignGroupController::class, 'index'])->name('groups.index');
        Route::post('/groups', [\App\Http\Controllers\CampaignGroupController::class, 'store'])->name('groups.store');
        Route::get('/groups/{campaignGroup}', [\App\Http\Controllers\CampaignGroupController::class, 'show'])->name('groups.show');
        Route::put('/groups/{campaignGroup}', [\App\Http\Controllers\CampaignGroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{campaignGroup}', [\App\Http\Controllers\CampaignGroupController::class, 'destroy'])->name('groups.destroy');
        Route::post('/groups/{campaignGroup}/emails', [\App\Http\Controllers\CampaignEmailController::class, 'store'])->name('group-emails.store');
        Route::put('/groups/{campaignGroup}/emails/{campaignEmail}', [\App\Http\Controllers\CampaignEmailController::class, 'update'])->name('group-emails.update');
        Route::delete('/groups/{campaignGroup}/emails/{campaignEmail}', [\App\Http\Controllers\CampaignEmailController::class, 'destroy'])->name('group-emails.destroy');

        // Position
        Route::get('/positions', [\App\Http\Controllers\ContactPositionController::class, 'index'])->name('positions.index');
        Route::post('/positions', [\App\Http\Controllers\ContactPositionController::class, 'store'])->name('positions.store');
        Route::get('/positions/create', [\App\Http\Controllers\ContactPositionController::class, 'create'])->name('positions.create');
        Route::get('/positions/{contactPosition}', [\App\Http\Controllers\ContactPositionController::class, 'show'])->name('positions.show');
        Route::put('/positions/{contactPosition}', [\App\Http\Controllers\ContactPositionController::class, 'update'])->name('positions.update');

        // Invoice
        Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/create', [\App\Http\Controllers\InvoiceController::class, 'create'])->name('invoices.create');
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
        Route::put('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'update'])->name('invoices.update');
        Route::post('/invoices/{invoice}/send', [\App\Http\Controllers\InvoiceController::class, 'send'])->name('invoices.send');


    });
});