<?php

namespace App\Nova\Info;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Name;
use App\Nova\Fields\StatusSelect;
use App\Nova\Info\Project\Project;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;

class Client extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Info\Client
     */
    public static $model = \App\Models\Info\Client::class;

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
    public static $group = 'Info';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'type',
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
            ID::make(),

            Name::make()
                ->requiredRule()
                ->translatable(),

            Select::make('type')
                  ->options(static::$model::trans('types'))
                  ->displayUsing(fn($q) => $q ? static::$model::trans("types.{$q}") : '-')
                  ->nullableRule()
                  ->default(fn($r) => $r->editing ? static::$model::DEFAULT_TYPE : null),

            HasMany::make(static::$model::trans('projects'), 'projects', Project::class),

            StatusSelect::forResource($this),
        ];
    }
}
