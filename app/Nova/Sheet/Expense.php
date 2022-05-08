<?php

namespace App\Nova\Sheet;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Info\EntryCategory;
use App\Nova\Info\Project\Project;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Textarea;

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
            ID::make(),

            Date::make(__('models/sheet/expense.fields.date'), 'date')
                ->default(fn($r) => $r->editing ? now() : null)
                ->requiredRule()
                ->showOnRelationships(),

            Currency::make(__('models/sheet/expense.fields.amount'), 'amount')
                    ->default(0)
                    ->requiredRule(),

            Boolean::make(__('models/sheet/expense.fields.vat_included'), 'vat_included')
                   ->hideFromIndex()
                   ->default(false),

            BelongsTo::make(static::$model::trans('project'), 'project', Project::class)
                     ->showCreateRelationButton()
                     ->searchable(true)
                     ->sortable(),

            BelongsTo::make(static::$model::trans('entry_category'), 'entry_category', EntryCategory::class)
                     ->showCreateRelationButton()
                     ->sortable(),

            Textarea::make(__('models/sheet/expense.fields.remarks'), 'remarks')
                    ->nullableRule(),

        ];
    }
}
