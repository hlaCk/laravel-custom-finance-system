<?php

namespace Sheets\CompanyReportYTD;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Sheets\CompanyReportYTD\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'company-report-y-t-d');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::provideToScript([
                                      'defaultFromDate'  => $defaultFromDate = getDefaultFromDate()->format('Y-m-d'),
                                      'defaultToDate'    => $defaultToDate = getDefaultToDate()->format('Y-m-d'),
                                      'defaultDateRange' => "{$defaultFromDate} - {$defaultToDate}",
                                  ]);
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if( $this->app->routesAreCached() ) {
            return;
        }

        Route::middleware([ 'nova', Authorize::class ])
             ->prefix('nova-vendor/company-report-y-t-d')
             ->group(__DIR__ . '/../routes/api.php');
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
