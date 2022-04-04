<?php

namespace App\Nova\Chart;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Fields\Field;
use App\Nova\Fields\StatusSelect;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class Account extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Chart\Account
     */
    public static $model = \App\Models\Chart\Account::class;

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
    public static $group = 'Chart';

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
            ID::make(__('ID'), 'id')
              ->sortable(),

            Field::Text(__('models/chart/account.fields.name'), 'name')
                 ->rules('required')
                 ->required(),

            Field::Select('type')
                 ->options(static::$model::trans('types'))
                 ->displayUsing(fn($q) => $q ? static::$model::trans("types.{$q}") : '-')
                 ->rules('nullable')
                 ->nullable()
                 ->default(static::$model::DEFAULT_TYPE),

            BelongsTo::make(static::$model::trans('parent_account'), 'account', Account::class)
                     ->showCreateRelationButton()
                     ->hideFromIndex()
                     ->nullable(),

            HasMany::make(static::$model::trans('sub_accounts'), 'sub_accounts', Account::class)
                   ->sortable(),

            StatusSelect::forResource($this),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return parent::indexQuery(
            $request,
            $query->when(!$request->viaRelationship(), fn($q) => $q->onlyParents())
        );
    }
}
