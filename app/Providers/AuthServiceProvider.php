<?php

namespace App\Providers;

use App\Nova\Info\Client;
use App\Nova\Info\EntryCategory;
use App\Nova\Settings\Setting;
use App\Policies\Info\ClientPolicy;
use App\Policies\Info\EntryCategoryPolicy;
use App\Policies\Settings\SettingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Abstracts\Model' => 'App\Policies\ModelPolicy',
        EntryCategory::class                        => EntryCategoryPolicy::class,
        \App\Nova\Chart\Account::class              => \App\Policies\Chart\AccountPolicy::class,
        \App\Nova\Info\Project\Project::class       => \App\Policies\Info\Project\ProjectPolicy::class,
        \App\Nova\Info\Project\ProjectStatus::class => \App\Policies\Info\Project\ProjectStatusPolicy::class,
        Client::class                               => ClientPolicy::class,
        Setting::class                              => SettingPolicy::class,
        \App\Nova\Sheet\Expense::class           => \App\Policies\Sheet\ExpensePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
