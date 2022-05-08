<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Fields\Abstracts\FieldCollection as AbstractFieldCollectionAlias;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldCollection extends AbstractFieldCollectionAlias
{
    /**
     * Filter fields for showing on detail.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param mixed                                   $resource
     *
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function filterForDetail(NovaRequest $request, $resource)
    {
        return $this->filter(function ($field) use ($resource, $request) {
            /** @var \Laravel\Nova\Fields\FieldElement $field */
            return ($field->isRelationshipView($request) ? $field->isShownOnRelationships($request, $resource)
                : $field->isShownOnDetail($request, $resource));
        })->values();
    }

    /**
     * Filter fields for showing on index.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param mixed                                   $resource
     *
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function filterForIndex(NovaRequest $request, $resource)
    {
        return $this->filter(function ($field) use ($resource, $request) {
            /** @var \Laravel\Nova\Fields\FieldElement $field */
//            return ($field->isRelationshipView($request) ? $field->isShownOnRelationships($request, $resource) : true) && $field->isShownOnIndex($request, $resource);
            return ($field->isRelationshipView($request) ? $field->isShownOnRelationships($request, $resource)
                : $field->isShownOnIndex($request, $resource));
        })->values();
    }

}
