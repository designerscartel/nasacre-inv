<?php

namespace App\Providers;

use App\Actions\Booking\CreateBookingInformation;
use App\Actions\Booking\CreateBookingPdf;
use App\Actions\Booking\DeleteBooking;
use App\Actions\Booking\UpdateBookingInformation;
use App\Actions\Booking\CreateBookingRefInformation;
use App\Actions\Sacre\UpdateSacreInformation;
use App\Actions\Sacre\CreateSacreInformation;
use App\Actions\Sacre\DeleteSacre;
use App\Contracts\Booking\CreatesBookingInformation;
use App\Contracts\Booking\CreatesBookingPdf;
use App\Contracts\Booking\CreatesBookingRefInformation;
use App\Contracts\Booking\DeletesBooking;
use App\Contracts\Booking\UpdatesBookingInformation;
use App\Contracts\Sacre\UpdatesSacreInformation;
use App\Contracts\Sacre\CreatesSacreInformation;
use App\Contracts\Sacre\DeletesSacre;

use App\Actions\SacreContact\AddSacreContact;
use App\Actions\SacreContact\UpdateSacreContact;
use App\Actions\SacreContact\DeleteSacreContact;
use App\Contracts\SacreContact\AddsSacreContact;
use App\Contracts\SacreContact\UpdatesSacreContact;
use App\Contracts\SacreContact\DeletesSacreContact;

use App\Actions\SacreFile\AddScareFile;
use App\Actions\SacreFile\UpdateScareFile;
use App\Actions\SacreFile\DeleteScareFile;
use App\Contracts\SacreFile\AddsScareFile;
use App\Contracts\SacreFile\UpdatesScareFile;
use App\Contracts\SacreFile\DeletesScareFile;

use App\Actions\SharedFile\AddSharedFile;
use App\Actions\SharedFile\UpdateSharedFile;
use App\Actions\SharedFile\DeleteSharedFile;
use App\Contracts\SharedFile\AddsSharedFile;
use App\Contracts\SharedFile\UpdatesSharedFile;
use App\Contracts\SharedFile\DeletesSharedFile;

use App\Actions\Region\UpdateRegionInformation;
use App\Actions\Region\CreateRegionInformation;
use App\Contracts\Region\UpdatesRegionInformation;
use App\Contracts\Region\CreatesRegionInformation;

use App\Actions\Campaign\UpdateCampaignInformation;
use App\Actions\Campaign\CreateCampaignInformation;
use App\Actions\Campaign\SendCampaignInformation;
use App\Contracts\Campaign\UpdatesCampaignInformation;
use App\Contracts\Campaign\CreatesCampaignInformation;
use App\Contracts\Campaign\SendsCampaignInformation;

use App\Actions\CampaignGroup\UpdateCampaignGroupInformation;
use App\Actions\CampaignGroup\CreateCampaignGroupInformation;
use App\Actions\CampaignGroup\DeleteCampaignGroup;
use App\Contracts\CampaignGroup\UpdatesCampaignGroupInformation;
use App\Contracts\CampaignGroup\CreatesCampaignGroupInformation;
use App\Contracts\CampaignGroup\DeletesCampaignGroup;

use App\Actions\CampaignEmail\UpdateCampaignEmailInformation;
use App\Actions\CampaignEmail\CreateCampaignEmailInformation;
use App\Actions\CampaignEmail\DeleteCampaignEmail;
use App\Contracts\CampaignEmail\UpdatesCampaignEmailInformation;
use App\Contracts\CampaignEmail\CreatesCampaignEmailInformation;
use App\Contracts\CampaignEmail\DeletesCampaignEmail;

use App\Actions\ContactPosition\UpdateContactPositionInformation;
use App\Actions\ContactPosition\CreateContactPositionInformation;
use App\Contracts\ContactPosition\UpdatesContactPositionInformation;
use App\Contracts\ContactPosition\CreatesContactPositionInformation;

use App\Actions\Invoice\UpdateInvoiceInformation;
use App\Actions\Invoice\CreateInvoiceInformation;
use App\Actions\Invoice\DeleteInvoice;
use App\Actions\Invoice\SendInvoiceInformation;
use App\Actions\Invoice\CreateInvoicePdf;
use App\Contracts\Invoice\UpdatesInvoiceInformation;
use App\Contracts\Invoice\CreatesInvoiceInformation;
use App\Contracts\Invoice\DeletesInvoice;
use App\Contracts\Invoice\SendsInvoiceInformation;
use App\Contracts\Invoice\CreatesInvoicePdf;

use App\Actions\SacreInvoice\UpdateSacreInvoiceInformation;
use App\Contracts\SacreInvoice\UpdatesSacreInvoiceInformation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        app()->singleton(CreatesSacreInformation::class, CreateSacreInformation::class);
        app()->singleton(UpdatesSacreInformation::class, UpdateSacreInformation::class);
        app()->singleton(DeletesSacre::class, DeleteSacre::class);

        app()->singleton(AddsSacreContact::class, AddSacreContact::class);
        app()->singleton(UpdatesSacreContact::class, UpdateSacreContact::class);
        app()->singleton(DeletesSacreContact::class, DeleteSacreContact::class);

        app()->singleton(AddsScareFile::class, AddScareFile::class);
        app()->singleton(UpdatesScareFile::class, UpdateScareFile::class);
        app()->singleton(DeletesScareFile::class, DeleteScareFile::class);

        app()->singleton(AddsSharedFile::class, AddSharedFile::class);
        app()->singleton(UpdatesSharedFile::class, UpdateSharedFile::class);
        app()->singleton(DeletesSharedFile::class, DeleteSharedFile::class);

        app()->singleton(CreatesRegionInformation::class, CreateRegionInformation::class);
        app()->singleton(UpdatesRegionInformation::class, UpdateRegionInformation::class);

        app()->singleton(CreatesCampaignInformation::class, CreateCampaignInformation::class);
        app()->singleton(UpdatesCampaignInformation::class, UpdateCampaignInformation::class);
        app()->singleton(SendsCampaignInformation::class, SendCampaignInformation::class);

        app()->singleton(CreatesCampaignGroupInformation::class, CreateCampaignGroupInformation::class);
        app()->singleton(UpdatesCampaignGroupInformation::class, UpdateCampaignGroupInformation::class);
        app()->singleton(DeletesCampaignGroup::class, DeleteCampaignGroup::class);

        app()->singleton(CreatesCampaignEmailInformation::class, CreateCampaignEmailInformation::class);
        app()->singleton(UpdatesCampaignEmailInformation::class, UpdateCampaignEmailInformation::class);
        app()->singleton(DeletesCampaignEmail::class, DeleteCampaignEmail::class);

        app()->singleton(CreatesContactPositionInformation::class, CreateContactPositionInformation::class);
        app()->singleton(UpdatesContactPositionInformation::class, UpdateContactPositionInformation::class);

        app()->singleton(CreatesInvoiceInformation::class, CreateInvoiceInformation::class);
        app()->singleton(UpdatesInvoiceInformation::class, UpdateInvoiceInformation::class);
        app()->singleton(DeletesInvoice::class, DeleteInvoice::class);
        app()->singleton(SendsInvoiceInformation::class, SendInvoiceInformation::class);
        app()->singleton(CreatesInvoicePdf::class, CreateInvoicePdf::class);

        app()->singleton(CreatesBookingInformation::class, CreateBookingInformation::class);
        app()->singleton(CreatesBookingRefInformation::class, CreateBookingRefInformation::class);
        app()->singleton(UpdatesBookingInformation::class, UpdateBookingInformation::class);
        app()->singleton(DeletesBooking::class, DeleteBooking::class);
        app()->singleton(CreatesBookingPdf::class, CreateBookingPdf::class);


        app()->singleton(UpdatesSacreInvoiceInformation::class, UpdateSacreInvoiceInformation::class);
    }
}
