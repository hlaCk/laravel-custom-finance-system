<?php

namespace App\Providers;

use App\Models\Info\User as UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Translation\Translator;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::script('global', public_path('js/global.js'));
//        Nova::script('sweetalert', public_path('js/sweetalert.min.js'));
        Nova::script('helpers', public_path('js/helpers.js'));
        Nova::script('globalEventListener', public_path('js/globalEventListener.js'));
        Nova::style('sidebar-icons', public_path('css/sidebar-icons.css'));

        Nova::serving(function (ServingNova $event) {
            if( $user = $event->request->user() ) {
                /** @var UserModel $user */
                $user->registerLocale();
            }
            Nova::provideToScript([
                                      'locale' => currentLocale(),
                                      'locale_name' => config('nova.locales.' . currentLocale(), '-'),
                                      'direction' => currentLocale() === 'ar' ? 'rtl' : 'ltr',
                                      'isRTL' => currentLocale() === 'ar',
                                      //                                      'toggleHiddenNavigation' => (array) config('navigation.toggle_hidden_key'),
                                      'version' => config('app.version'),
                                      'zero_money' => formatValueAsCurrency(0),
                                  ]);

            //Nova::translations(__(""));
        });

//        Nova::serving(function(ServingNova $event) {
//            if( $user = $event->request->user() ) {
//                /** @var UserModel $user */
//                $user->registerLocale();
//            }
//        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        optional(auth()->user())->registerLocale();

        return [
            // new Help,
            \Vink\NovaCacheCard\CacheCard::make()
                                         ->canSee(fn() => config('app.dev_mode', false)),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Nova::sortResourcesBy(function ($resource) {
            return method_exists($resource, 'priority') ? $resource::priority() : 99999;
        });

        $this->app->singleton('translation-loader-local', function ($app) {
            $translation_manager_local = config('translation-loader.translation-loader-local');

            return $translation_manager_local && class_exists($translation_manager_local)
                ?
                (new $translation_manager_local($app[ 'files' ], $app[ 'path.lang' ]))
                :
                ($app[ 'translation.loader' ] ?? new \Spatie\TranslationLoader\TranslationLoaderManager(
                        $app[ 'files' ],
                        $app[ 'path.lang' ]
                    ));
        });

        $this->app->singleton('translator-local', function ($app) {
            $loader = $app[ 'translation-loader-local' ];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app[ 'config' ][ 'app.locale' ];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app[ 'config' ][ 'app.fallback_locale' ]);

            return $trans;
        });
    }

    protected function resources()
    {
        Nova::resources(getNovaResources('Nova,Nova/*,Nova/*/*'));
//        Nova::resources([
//                            User::class,
//                            Client::class,
//                            EntryCategory::class,
//                            Project::class,
//                            Setting::class,
//                            Role::class,
//                            Permission::class,
//                        ]);
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            // ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
//            new \Anaseqal\NovaSidebarIcons\NovaSidebarIcons(),

//            new \Bernhardh\NovaTranslationEditor\NovaTranslationEditor(),

//            new \OptimistDigital\MenuBuilder\MenuBuilder,

\Eolica\NovaLocaleSwitcher\LocaleSwitcher::make()
                                         ->setLocales(config('nova.locales'))
                                         ->onSwitchLocale(function (Request $request) {
                                             if( isLocaleAllowed($locale = $request->post('locale')) ) {
                                                 $session = session();
                                                 $session->put([ 'locale' => $locale ]);
                                                 $session->save();
                                             }
                                         }),

//            \ChrisWare\NovaBreadcrumbs\NovaBreadcrumbs::make(),

//            new \Anaseqal\NovaImport\NovaImport,

\Sheets\YearToDate\YearToDate::make(),
        ];
    }

}
