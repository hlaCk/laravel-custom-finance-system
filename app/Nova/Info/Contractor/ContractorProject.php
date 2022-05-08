<?php

namespace App\Nova\Info\Contractor;

use App\Nova\Abstracts\Resource as BaseResource;
use App\Nova\Info\Project\Project;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Makeable;

class ContractorProject extends BaseResource
{
    /**
     * The visual style used for the table. Available options are 'tight' and 'default'.
     *
     * @var string
     */
    public static $tableStyle = 'tight';
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string|\App\Models\Info\Contractor\Contractor
     */
    public static $model = \App\Models\Info\Contractor\ContractorProject::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'date';

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
    ];
    /**
     * The columns that should be hidden on relationship index view.
     *
     * @var array
     */
    public static $hideAttributesFromRelationshipsIndex = [];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request|\Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function fields(Request $request)
    {
        return $this->parseFields($request, [
            ID::make(),
            'mergeWhenCurrentResourceIs,' . static::class => [
                BelongsTo::make(
                    __('models/info/contractor/contractor.singular'),
                    'contractor',
                    Contractor::class
                )->requiredRule(),

                BelongsTo::make(
                    __('models/info/project/project.singular'),
                    'project',
                    Project::class
                )->requiredRule(),
            ],
        ]);
    }

    /**
     * Get the fields displayed by the resource in relationships.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public static function rawFieldsForRelationships(Request $request, ?bool $for_pivot = null): array
    {
        $for_pivot ??= $request->viaRelationship();
        return [
            Date::make('date')
                ->default(fn($r) => $r->editing ? now() : null)
                ->nullableRule()
                ->showOnRelationships(),

            Textarea::make('remarks')
                    ->nullableRule()
                    ->onlyOnForms(),

            Text::make('unit')
                ->nullableRule()
                ->showOnRelationships(),

            Number::make('quantity')
                  ->requiredRule()
                  ->showOnRelationships(),

            Currency::make('price')
                    ->requiredRule()
                    ->step(1)
                    ->showOnRelationships(),

            Currency::make('total')
                    ->exceptOnForms()
                    ->showOnRelationships(),
        ];
    }
}
