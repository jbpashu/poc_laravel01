<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImportV1CustomersProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\ImportV1Customers', function ($app) {
            return new ImportV1Customers();
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
