<?php

namespace App\Providers;

use App\Actions\Sacre\UpdateSacreInformation;
use App\Contracts\Sacre\UpdatesSacreInformation;

use App\Actions\Region\UpdateRegionInformation;
use App\Contracts\Region\UpdatesRegionInformation;

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

        app()->singleton(CreatesRegionInformation::class, CreateRegionInformation::class);
        app()->singleton(UpdatesRegionInformation::class, UpdateRegionInformation::class);
    }
}
