<?php

namespace App\Providers;

use App\Extensions\CacheEloquentProvider;
use Illuminate\Support\ServiceProvider;

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
        $this->app['auth']->provider(
            'eloquent',
            function ($app, $config) {
                return new CacheEloquentProvider(
                    $this->app['hash'],
                    $config['model']
                );
            }
        );
    }
}
