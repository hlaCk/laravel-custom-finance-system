<?php

namespace App\Nova\Info\Project;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Name;
use App\Nova\Fields\StatusSelect;
use App\Nova\Info\Client;
use App\Nova\Info\Contractor\Contractor;
use App\Nova\Info\Contractor\ContractorProject;
use App\Nova\Sheet\Credit;
use App\Nova\Sheet\Expense;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;

class Project extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Info\Project\Project
     */
    public static $model = \App\Models\Info\Project\Project::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Project';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function fields($request)
    {
        return [
            ID::make(),

            Name::make()
                ->requiredRule(),

            Currency::make('base_cost')
                    ->default(0)
                    ->requiredRule(),

            Currency::make('cost', fn() => $this->cost)
                    ->exceptOnForms(),

            BelongsTo::make(
                'client',
                Client::class
            )
                     ->nullableRule(),

            BelongsTo::make(
                'project_status',
                ProjectStatus::class
            )
                     ->default(
                         fn($r) => $r->editing ? \App\Models\Info\Project\ProjectStatus::byName(
                             static::$model::DEFAULT_PROJECT_STATUS_NAME
                         )->first()->id : null
                     ),

            HasMany::make('project_costs', ProjectCost::class),

            BelongsToMany::make('contractors', Contractor::class)
                         ->fields(fn($r) => ContractorProject::getFieldsForRelationships($r)),

            HasMany::make('credits', Credit::class),

            HasMany::make('expenses', Expense::class),

            StatusSelect::forResource($this),
        ];
    }
}
