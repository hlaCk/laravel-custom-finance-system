<?php

namespace App\Nova\Info\Project;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Field;
use App\Nova\Fields\StatusSelect;
use App\Nova\Info\Client;
use App\Nova\Info\Contractor\Contractor;
use App\Nova\Sheet\Credit;
use App\Nova\Sheet\Expense;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
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
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')
              ->sortable(),

            Field::Text(__('models/info/project/project.fields.name'), 'name')
                 ->rules('required')
                 ->required(),

            Field::Currency(__('models/info/project/project.fields.base_cost'), 'base_cost')
                 ->default(0)
                 ->rules('required')
                 ->required(),

            Field::Currency(__('models/info/project/project.fields.cost'), 'cost', fn()=>$this->cost_label)
                 ->exceptOnForms(),

            BelongsTo::make(
                __('models/info/project/project.fields.client'),
                'client',
                Client::class
            )
                     ->nullable()
                     ->showCreateRelationButton(),

            BelongsTo::make(
                __('models/info/project/project.fields.project_status'),
                'project_status',
                ProjectStatus::class
            )
                     ->default(
                         fn() => \App\Models\Info\Project\ProjectStatus::where(
                             'name',
                             static::$model::DEFAULT_PROJECT_STATUS_NAME
                         )->first()->id
                     )
                     ->showCreateRelationButton(),

            Field::HasMany(static::$model::trans('project_costs'), 'project_costs', ProjectCost::class),

            BelongsToMany::make(
                __('models/info/project/project.fields.contractors'),
                'contractors',
                Contractor::class
            ),

            Field::HasMany(static::$model::trans('credits'), 'credits', Credit::class),

            Field::HasMany(static::$model::trans('expenses'), 'expenses', Expense::class),

            StatusSelect::forResource($this),
        ];
    }
}
