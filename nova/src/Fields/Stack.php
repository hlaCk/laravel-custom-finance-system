<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Fields\Abstracts\Stack as AbstractStack;
use Laravel\Nova\Http\Requests\NovaRequest;

class Stack extends AbstractStack
{

    /**
     * Prepare each line for serialization.
     *
     * @param mixed  $resource
     * @param string $attribute
     *
     * @return void
     */
    public function prepareLines($resource, $attribute = null)
    {
        $this->ensureLinesAreResolveable();

        $request = app(NovaRequest::class);

        $this->lines = $this->lines->filter(function ($field) use ($request, $resource) {
            /** @var \Laravel\Nova\Fields\FieldElement $field */
            if( $request->isResourceIndexRequest() ) {
//                return ($field->isRelationshipView($request) ? $field->isShownOnRelationships($request, $resource) : true) && $field->isShownOnIndex($request, $resource);
                return ($field->isRelationshipView($request) ? $field->isShownOnRelationships($request, $resource)
                    : $field->isShownOnIndex($request, $resource));
            }

//            return ($field->isRelationshipView($request) ? $field->isShownOnRelationships($request, $resource) : true) && $field->isShownOnDetail($request, $resource);
            return ($field->isRelationshipView($request) ? $field->isShownOnRelationships($request, $resource)
                : $field->isShownOnDetail($request, $resource));
        })->values()->each->resolveForDisplay($resource, $attribute);
    }
}
