<?php

namespace App\Nova\Settings;

use App\Nova\Abstracts\Resource;
use App\Nova\Fields\Field;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;

class Permission extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Settings\Permission
     */
    public static $model = \App\Models\Settings\Permission::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static $group = 'Settings';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'group',
        'guard_name',
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

            Field::Text(__('models/settings/permission.fields.name'), 'name')
                 ->rules('required')
                 ->required(),

            Field::Text(__('models/settings/permission.fields.guard_name'), 'guard_name')
                 ->rules('required')
                 ->required(),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    public static function icon()
    {
        return "<img src='/images/circle_copy.svg' class='inner-nav-icon'>";
    }
}
