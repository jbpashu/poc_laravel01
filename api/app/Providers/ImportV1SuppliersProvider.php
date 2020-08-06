<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\ImportV1Suppliers;

class ImportV1SuppliersProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\ImportV1Suppliers', function ($app) {
            return new ImportV1Suppliers();
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
