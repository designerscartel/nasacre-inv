<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

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
    // return view('welcome');
    return redirect('dashboard');
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
        Route::get('/sacres/{sacre}/invoices', [\App\Http\Controllers\SacreInvoiceControlller::class, 'show'])->name('sacre-invoices.show');
        Route::put('/sacres/{sacre}/invoices/{sacreInvoice}', [\App\Http\Controllers\SacreInvoiceControlller::class, 'update'])->name('sacre-invoices.update');
        Route::get('/sacres/{sacre}/invoices/{sacreInvoice}/pdf', [\App\Http\Controllers\SacreInvoiceControlller::class, 'pdf'])->name('sacre-invoices.pdf');

        // Files
        Route::post('/sacres/{sacre}/files', [\App\Http\Controllers\SacreFileController::class, 'store'])->name('sacre-files.store');
        Route::get('/sacres/{sacre}/files/{file}', [\App\Http\Controllers\SacreFileController::class, 'show'])->name('sacre-files.store');
        Route::put('/sacres/{sacre}/files/{file}', [\App\Http\Controllers\SacreFileController::class, 'update'])->name('sacre-files.update');
        Route::delete('/sacres/{sacre}/files/{file}', [\App\Http\Controllers\SacreFileController::class, 'destroy'])->name('sacre-files.destroy');

        //Shared Files
        Route::post('/sacres/{sacre}/shared', [\App\Http\Controllers\SharedFileController::class, 'store'])->name('share-files.store');
        Route::get('/sacres/{sacre}/shared/{file}', [\App\Http\Controllers\SharedFileController::class, 'show'])->name('share-files.store');
        Route::put('/sacres/{sacre}/shared/{file}', [\App\Http\Controllers\SharedFileController::class, 'update'])->name('share-files.update');
        Route::delete('/sacres/{sacre}/shared/{file}', [\App\Http\Controllers\SharedFileController::class, 'destroy'])->name('share-files.destroy');

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

        // Booking
        Route::get('/bookings', [\App\Http\Controllers\BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [\App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/create', [\App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
        Route::get('/bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('bookings.show');
        Route::put('/bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'update'])->name('bookings.update');
        Route::get('/bookings/{sacreBooking}/pdf', [\App\Http\Controllers\BookingController::class, 'pdf'])->name('bookings.pdf');
        Route::put('/bookings/{booking}/amend/{sacreBooking}', [\App\Http\Controllers\BookingController::class, 'amend'])->name('bookings.amend');

        Route::get('/bookings/{booking}/edit', [\App\Http\Controllers\BookingController::class, 'edit'])->name('bookings.edit');
        // Route::post('/bookings/{booking}/send', [\App\Http\Controllers\BookingController::class, 'send'])->name('bookings.send');

    });
});