<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\CategoryInterface', 'App\Repositories\CategoryRepository');
        $this->app->bind('App\Interfaces\TagInterface', 'App\Repositories\TagRepository');
        $this->app->bind('App\Interfaces\NewsInterface', 'App\Repositories\NewsRepository');
        $this->app->bind('App\Interfaces\SubCategoryInterface', 'App\Repositories\SubCategoryRepository');
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
