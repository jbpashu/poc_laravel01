<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\ImportV1Orders;

class ImportV1OrdersProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\ImportV1Orders', function ($app) {
            return new ImportV1Orders();
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
