<?php

namespace App\Providers;

use App\Models\Info\Client;
use App\Models\Info\CreditCategory;
use App\Models\Info\EntryCategory;
use App\Models\Settings\Setting;
use App\Policies\Info\ClientPolicy;
use App\Policies\Info\CreditCategoryPolicy;
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
        EntryCategory::class                                    => EntryCategoryPolicy::class,
        CreditCategory::class                                   => CreditCategoryPolicy::class,
        \App\Models\Chart\Account::class                        => \App\Policies\Chart\AccountPolicy::class,
        \App\Models\Info\Contractor\Contractor::class           => \App\Policies\Info\Contractor\ContractorPolicy::class,
        \App\Models\Info\Contractor\ContractorSpeciality::class => \App\Policies\Info\Contractor\ContractorSpecialityPolicy::class,
        \App\Models\Info\Project\Project::class                 => \App\Policies\Info\Project\ProjectPolicy::class,
        \App\Models\Info\Project\ProjectCost::class             => \App\Policies\Info\Project\ProjectCostPolicy::class,
        \App\Models\Info\Project\ProjectStatus::class           => \App\Policies\Info\Project\ProjectStatusPolicy::class,
        Client::class                                           => ClientPolicy::class,
        Setting::class                                          => SettingPolicy::class,
        \App\Models\Sheet\Expense::class                        => \App\Policies\Sheet\ExpensePolicy::class,
        \App\Models\Sheet\Credit::class                         => \App\Policies\Sheet\CreditPolicy::class,
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
