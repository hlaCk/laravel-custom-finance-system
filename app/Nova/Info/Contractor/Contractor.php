<?php

namespace App\Nova\Info\Contractor;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Field;
use App\Nova\Fields\StatusSelect;
use App\Nova\Info\Project\Project;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;

class Contractor extends BaseResource
{
    /**
     * The visual style used for the table. Available options are 'tight' and 'default'.
     *
     * @var string
     */
    public static $tableStyle = 'tight';

    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Info\Contractor\Contractor
     */
    public static $model = \App\Models\Info\Contractor\Contractor::class;

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
    public static $group = 'Contractors';

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

            Field::Text(__('models/info/contractor/contractor.fields.name'), 'name')
                 ->rules('required')
                 ->required(),

            BelongsTo::make(
                __('models/info/contractor/contractor.fields.contractor_speciality'),
                'contractor_speciality',
                ContractorSpeciality::class
            )
                     ->nullableRule()
                     ->showCreateRelationButton(),

            BelongsToMany::make(static::$model::trans('projects'), 'projects', Project::class),

            StatusSelect::forResource($this),
        ];
    }
}
