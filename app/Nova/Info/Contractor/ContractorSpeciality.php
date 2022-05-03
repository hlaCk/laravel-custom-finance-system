<?php

namespace App\Nova\Info\Contractor;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Field;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;

class ContractorSpeciality extends BaseResource
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
     * @var string|\App\Models\Info\Contractor\ContractorSpeciality
     */
    public static $model = \App\Models\Info\Contractor\ContractorSpeciality::class;

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

            Field::Text(__('models/info/contractor/contractor_speciality.fields.name'), 'name')
                 ->requiredRule(),

            Field::HasMany(static::$model::trans('contractors'), 'contractors', Contractor::class),

        ];
    }
}
