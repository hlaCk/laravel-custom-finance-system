<?php

namespace App\Nova\Info\Project;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Field;
use App\Nova\Fields\StatusSelect;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;

class ProjectStatus extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Info\Project\ProjectStatus
     */
    public static $model = \App\Models\Info\Project\ProjectStatus::class;

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

            Field::Text(__('models/info/project/project_status.fields.name'), 'name')
                 ->rules('required')
                 ->required(),
        ];
    }
}
