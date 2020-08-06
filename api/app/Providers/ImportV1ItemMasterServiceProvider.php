<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImportV1ItemMasterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\ImportV1Items', function ($app) {
            return new \App\Library\Services\ImportV1Items();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
