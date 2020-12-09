<?php

namespace App\Providers;

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
        $this->app->bind(
            \App\Repositories\Paygates\PaygateRepositoryInterface::class,
            \App\Repositories\Paygates\PaygateEloquentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Widgets\WidgetRepositoryInterface::class,
            \App\Repositories\Widgets\WidgetEloquentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Users\UserRepositoryinterface::class,
            \App\Repositories\Users\UserEloquentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Menus\MenusRepositoryInterface::class,
            \App\Repositories\Menus\MenusEloquentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
