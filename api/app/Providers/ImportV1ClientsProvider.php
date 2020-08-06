<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImportV1ClientsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\ImportV1Clients', function ($app) {
            return new \App\Library\Services\ImportV1Clients();
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
