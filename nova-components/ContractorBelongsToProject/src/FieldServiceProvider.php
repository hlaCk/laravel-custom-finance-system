<?php

namespace Info\Components;

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
            Nova::script('contractor-belongs-to-project', __DIR__.'/../dist/js/field.js');
            Nova::style('contractor-belongs-to-project', __DIR__.'/../dist/css/field.css');
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
            ->prefix('nova-vendor/components/contractor-belongs-to-project')
            ->group(__DIR__.'/../routes/api.php');
    }
}
