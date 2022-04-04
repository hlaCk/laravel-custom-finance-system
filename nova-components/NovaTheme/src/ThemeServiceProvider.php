<?php

namespace Nova\NovaTheme;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::booted(function () {
            Nova::theme(asset('/nova/nova-theme/theme.css'));
            Nova::theme(asset('/nova/nova-theme/custom.css'));
        });

        $this->publishes([
            __DIR__.'/../resources/css' => public_path('nova/nova-theme'),
//            __DIR__.'/../resources/scss' => resource_path('scss'),
        ], ['public','nova-theme']);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
