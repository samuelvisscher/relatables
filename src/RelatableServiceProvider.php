<?php

namespace Visscher\Relatables;

use Event;
use Illuminate\Support\ServiceProvider;

class RelatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('eloquent.saved: *', 'Visscher\Relatables\RelatableListener');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
