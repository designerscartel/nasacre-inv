<?php

namespace App\Providers;

use App\Actions\Sacre\UpdateSacreInformation;
use App\Actions\Sacre\CreateSacreInformation;
use App\Actions\SacreContact\AddSacreContact;
use App\Actions\SacreContact\UpdateSacreContact;
use App\Contracts\Sacre\UpdatesSacreInformation;
use App\Contracts\Sacre\CreatesSacreInformation;
use App\Contracts\SacreContact\AddsSacreContact;
use App\Contracts\SacreContact\UpdatesSacreContact;

use App\Actions\Region\UpdateRegionInformation;
use App\Actions\Region\CreateRegionInformation;
use App\Contracts\Region\UpdatesRegionInformation;
use App\Contracts\Region\CreatesRegionInformation;

use App\Actions\ContactPosition\UpdateContactPositionInformation;
use App\Actions\ContactPosition\CreateContactPositionInformation;
use App\Contracts\ContactPosition\UpdatesContactPositionInformation;
use App\Contracts\ContactPosition\CreatesContactPositionInformation;


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
        app()->singleton(AddsSacreContact::class, AddSacreContact::class);
        app()->singleton(UpdatesSacreContact::class, UpdateSacreContact::class);


        app()->singleton(CreatesRegionInformation::class, CreateRegionInformation::class);
        app()->singleton(UpdatesRegionInformation::class, UpdateRegionInformation::class);


        app()->singleton(CreatesContactPositionInformation::class, CreateContactPositionInformation::class);
        app()->singleton(UpdatesContactPositionInformation::class, UpdateContactPositionInformation::class);

    }
}
