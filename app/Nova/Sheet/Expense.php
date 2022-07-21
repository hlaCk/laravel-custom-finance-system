<?php

namespace App\Nova\Sheet;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Info\EntryCategory;
use App\Nova\Info\Project\Project;
use Epartment\NovaDependencyContainer\NovaDependencyContainer as Depender;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
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
     * @param \Illuminate\Http\Request|\Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function fields(Request $request)
    {
        $fillContractor = function ($request, $model, $attribute, $requestAttribute) {
            /** @var \App\Models\Info\Contractor\Contractor $model */
            /** @var \Laravel\Nova\Http\Requests\NovaRequest $request */
            $newValue = $request->get($requestAttribute);
            $pass_project = false;
            $pass_entry_category = false;

            if( $entry_category_id =
                head(array_filter($request->only([ 'entry_category', 'entry_category_id' ])))
                    ?: $model->entry_category ) {
                $pass_entry_category = EntryCategory::newModel()->isHasContractor($entry_category_id);
            }
            if( $project_id =
                head(array_filter($request->only([ 'project', 'project_id' ]))) ?: $model->project ) {
                $pass_project =
                    (isModel($project_id) ? $project_id : Project::newModel()->find($project_id))
                        ->contractors()->whereKey($newValue)->count() > 0;
            }

            $model->setAttribute($attribute, $pass_project && $pass_entry_category ? $newValue : 0);

            return $model;
        };

        return [
            ID::make(),

            Date::make(static::trans('date'), 'date')
                ->default(fn($r) => $r->editing ? now() : null)
                ->requiredRule()
                ->showOnRelationships(),

            Currency::make(static::trans('amount'), 'amount')
                    ->default(0)
                    ->requiredRule(),

            Boolean::make(static::trans('vat_included'), 'vat_included')
                   ->hideFromIndex()
                   ->default(false),

            BelongsTo::make(static::trans('project'), 'project', Project::class)
                     ->showCreateRelationButton()
                     ->sortable()
                     ->requiredRule(),

            BelongsTo::make(static::trans('entry_category'), 'entry_category', EntryCategory::class)
                     ->showCreateRelationButton()
                     ->sortable()
                     ->requiredRule(),

            $this->getBelongsToDepend(
                'contractor',
                $fillContractor,
                [
                    'entry_category.entry_category_id',
                    EntryCategory::newModel()->onlyHasContractor()->toIndexedArrayPluck('id'),
                ]
            ),

            Textarea::make(static::trans('remarks'), 'remarks')
                    ->nullableRule(),

        ];
    }

    public function getBelongsToDepend(
        $relationName,
        ?\Closure $fillUsing = null,
        $dependsOn = null,
        $relationColumn = null,
        $resource = null
    ) {
        /** @var \App\Nova\Abstracts\Resource|\Closure|string|null $resource */
        /** @var \App\Models\Abstracts\Model $resourceModel */
        $fillUsing ??= function ($request, $model, $attribute, $requestAttribute) {
            $model->setAttribute($attribute, $request->get($requestAttribute));

            return $model;
        };

        $relationName = (string) value($relationName);
        $resource ??= str_plural($relationName);
        $resource = is_string($resource = value($resource)) ? getNovaResourceByUriKey($resource) : $resource;
        $resource = is_object($resource) ? get_class($resource ?? static::class) : $resource;
        $resourceModel = $resource::newModel();
        $label = $resource::trans('singular');
        $relationColumn = value($relationColumn) ?: "{$relationName}_" . $resourceModel->getKeyName();

        $belongs_to = BelongsTo::make($label, $relationName, $resource)->exceptOnForms();
        $select = Select::make($label, $relationColumn)
                        ->options($resourceModel->toArrayPluck($resource::$title))
                        ->nullableRule()
                        ->fillUsing($fillUsing);

        $depender = Depender::make([ $select ])->onlyOnForms();
        if( $dependsOn ) {
            $dependsOn = array_wrap($dependsOn);
            $depender = $depender->dependsOn(...$dependsOn);
        }

        return new MergeValue([ $belongs_to, $depender ]);
    }
}
