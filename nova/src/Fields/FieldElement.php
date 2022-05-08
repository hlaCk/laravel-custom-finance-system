<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Fields\Abstracts\FieldElement as AbstractFieldElement;
use Laravel\Nova\Http\Requests\NovaRequest;

abstract class FieldElement extends AbstractFieldElement
{

    /**
     * Indicates if the element should be shown on Relationships index & details view.
     *
     * @var \Closure|bool
     */
    public $showOnRelationships = false;

    /**
     * Which resource to apply $showOnRelationships on
     *
     * @var \Closure|array|null
     */
    public $showOnRelationshipsList = [];

    /**
     * Check if current request is for relationships.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return bool
     */
    public function isRelationshipView(NovaRequest $request): bool
    {
        return ($request->editing && in_array($request->editMode, [ 'attach', 'update-attached' ])) ||
               $request->relationshipType === 'hasMany' ||
               $request->viaResource ||
               $request->relatedResource;
    }

    /**
     * Check showing on relationships.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param mixed                                   $resource
     *
     * @return bool
     */
    public function isShownOnRelationships(NovaRequest $request, $resource): bool
    {
        if( is_callable($this->showOnRelationships) ) {
            $this->showOnRelationships = call_user_func($this->showOnRelationships, $request, $resource);
        }

        return $this->showOnRelationships;
    }

    /**
     * Specify that the element should only be shown on the relationships index & details view.
     *
     * @param \Closure|array|null $relationships
     *
     * @return $this
     */
    public function onlyOnRelationships($relationships = [])
    {
        $this->showOnIndex()
             ->showOnDetail()
             ->hideWhenCreating()
             ->hideWhenUpdating()
             ->showOnRelationships($relationships);

        return $this;
    }

    /**
     * Specify that the element should be shown in relationships index & details view.
     *
     * @param \Closure|array|null $relationships
     * @param \Closure|bool       $callback
     *
     * @return $this
     */
    public function showOnRelationships($relationships = [], $callback = true)
    {
        if( is_array($callback) ) {
            $_relationships = $callback;
            /** @var \Closure|bool $relationships */
            $callback = $relationships;
            $relationships = $_relationships;
        }

        if( is_bool($relationships) ) {
            $_callback = $relationships;
            /** @var \Closure|array|null $callback */
            $relationships = $callback;
            $callback = $_callback;
        }

        $this->showOnRelationshipsList = $relationships;
        $this->showOnRelationships = function (NovaRequest $request) use ($callback) {
            return $this->isRelatedResourceIn($request) && (is_callable($callback) ? call_user_func_array(
                    $callback,
                    func_get_args()
                )
                    : $callback);
        };

        return $this;
    }

    /**
     * Check if current request is for relationships.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Closure|array|null                     $resources
     *
     * @return bool|mixed
     */
    public function isRelatedResourceIn(NovaRequest $request, $resources = null)
    {
        $resources ??= $this->showOnRelationshipsList;
        if( empty($resources = value($resources)) ) {
            return true;
        }

        $related = $request->viaResource ? $request->viaResource() : null;
        $related ??= $request->relatedResource ? $request->relatedResource() : null;
        return in_array($related, $resources);
    }

    /**
     * Specify that the element should be hidden from relationships index & details.
     *
     * @param \Closure|array|null $relationships
     *
     * @return $this
     */
    public function exceptOnRelationships($relationships = [])
    {
        $this->showOnIndex()
             ->showOnDetail()
             ->showOnCreating()
             ->showOnUpdating()
             ->hideFromRelationships($relationships);

        return $this;
    }

    /**
     * Specify that the element should be hidden in relationships index & details view.
     *
     * @param \Closure|array|null $relationships
     * @param \Closure|bool       $callback
     *
     * @return $this
     */
    public function hideFromRelationships($relationships = [], $callback = true)
    {
        if( is_array($callback) ) {
            $_relationships = $callback;
            /** @var \Closure|bool $relationships */
            $callback = $relationships;
            $relationships = $_relationships;
        }

        if( is_bool($relationships) ) {
            $_callback = $relationships;
            /** @var \Closure|array|null $callback */
            $relationships = $callback;
            $callback = $_callback;
        }

        $this->showOnRelationshipsList = $relationships;
        $this->showOnRelationships = function (NovaRequest $request) use ($callback) {
            return !($this->isRelatedResourceIn($request) && (is_callable($callback) ? call_user_func_array(
                    $callback,
                    func_get_args()
                ) : $callback));
        };

        return $this;
    }

}
