<?php

namespace App\Nova\Sheet;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Field;
use App\Nova\Info\EntryCategory;
use App\Nova\Info\Project\Project;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;

class Expense extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Sheet\Expense
     */
    public static $model = \App\Models\Sheet\Expense::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'amount';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Sheet';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'date',
        'amount',
        'remarks',
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

            Field::Date(__('models/sheet/expense.fields.date'), 'date')
                 ->rules('required')
                 ->default(now())
                 ->required(),

            Field::Currency(__('models/sheet/expense.fields.amount'), 'amount')
                 ->default(0)
                 ->rules('required')
                 ->required(),

            Field::Boolean(__('models/sheet/expense.fields.vat_included'), 'vat_included')
                 ->hideFromIndex()
                 ->default(false),

            BelongsTo::make(static::$model::trans('project'), 'project', Project::class)
                     ->showCreateRelationButton()
                     ->searchable(true)
                     ->sortable(),

            BelongsTo::make(static::$model::trans('entry_category'), 'entry_category', EntryCategory::class)
                     ->showCreateRelationButton()
                     ->sortable(),

            Field::Textarea(__('models/sheet/expense.fields.remarks'), 'remarks')
                 ->rules('nullable')
                 ->nullable(),

        ];
    }
}
