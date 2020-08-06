<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImportV1WarehousesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\ImportV1Warehouses', function ($app) {
            return new ImportV1Warehouses();
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
