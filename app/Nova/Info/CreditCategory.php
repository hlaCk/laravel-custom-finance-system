<?php

namespace App\Nova\Info;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Name;
use App\Nova\Fields\StatusSelect;
use App\Nova\Sheet\Credit;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;

class CreditCategory extends BaseResource
{
    /**
     * To make the resource ($globallySearchable = false, $tableStyle = tight)
     *
     * @var bool
     */
    public static bool $partialResource = true;

    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Info\CreditCategory
     */
    public static $model = \App\Models\Info\CreditCategory::class;

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
                ->requiredRule(),

            HasMany::make(static::$model::trans('credits'), 'credits', Credit::class),

            StatusSelect::forResource($this),
        ];
    }
}
