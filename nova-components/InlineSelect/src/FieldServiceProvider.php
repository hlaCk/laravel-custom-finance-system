<?php

namespace Codi\InlineSelect;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });
        Nova::serving(function (ServingNova $event) {
            Nova::script('inline-select', __DIR__.'/../dist/js/field.js');
            Nova::style('inline-select', __DIR__.'/../dist/css/field.css');
        });
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

    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }
        Route::middleware(['nova'])
            ->prefix('nova-vendor/update-order')
            ->group(__DIR__.'/../routes/api.php');
    }
}
