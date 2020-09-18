<?php

namespace App\Providers;

use App\Actions\Sacre\UpdateSacreInformation;
use App\Actions\Sacre\CreateSacreInformation;
use App\Actions\Sacre\DeleteSacre;
use App\Contracts\Sacre\UpdatesSacreInformation;
use App\Contracts\Sacre\CreatesSacreInformation;
use App\Contracts\Sacre\DeletesSacre;

use App\Actions\SacreContact\AddSacreContact;
use App\Actions\SacreContact\UpdateSacreContact;
use App\Actions\SacreContact\DeleteSacreContact;
use App\Contracts\SacreContact\AddsSacreContact;
use App\Contracts\SacreContact\UpdatesSacreContact;
use App\Contracts\SacreContact\DeletesSacreContact;

use App\Actions\Region\UpdateRegionInformation;
use App\Actions\Region\CreateRegionInformation;
use App\Contracts\Region\UpdatesRegionInformation;
use App\Contracts\Region\CreatesRegionInformation;

use App\Actions\Campaign\UpdateCampaignInformation;
use App\Actions\Campaign\CreateCampaignInformation;
use App\Contracts\Campaign\UpdatesCampaignInformation;
use App\Contracts\Campaign\CreatesCampaignInformation;

use App\Actions\CampaignGroup\UpdateCampaignGroupInformation;
use App\Actions\CampaignGroup\CreateCampaignGroupInformation;
use App\Contracts\CampaignGroup\UpdatesCampaignGroupInformation;
use App\Contracts\CampaignGroup\CreatesCampaignGroupInformation;

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
        app()->singleton(DeletesSacre::class, DeleteSacre::class);

        app()->singleton(AddsSacreContact::class, AddSacreContact::class);
        app()->singleton(UpdatesSacreContact::class, UpdateSacreContact::class);
        app()->singleton(DeletesSacreContact::class, DeleteSacreContact::class);

        app()->singleton(CreatesRegionInformation::class, CreateRegionInformation::class);
        app()->singleton(UpdatesRegionInformation::class, UpdateRegionInformation::class);

        app()->singleton(CreatesCampaignInformation::class, CreateCampaignInformation::class);
        app()->singleton(UpdatesCampaignInformation::class, UpdateCampaignInformation::class);

        app()->singleton(CreatesCampaignGroupInformation::class, CreateCampaignGroupInformation::class);
        app()->singleton(UpdatesCampaignGroupInformation::class, UpdateCampaignGroupInformation::class);

        app()->singleton(CreatesContactPositionInformation::class, CreateContactPositionInformation::class);
        app()->singleton(UpdatesContactPositionInformation::class, UpdateContactPositionInformation::class);

    }
}
