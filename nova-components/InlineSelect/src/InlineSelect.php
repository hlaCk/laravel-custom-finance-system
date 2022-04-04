<?php

namespace Codi\InlineSelect;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class InlineSelect extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'inline-select';

    /**
     * Create a new field.
     *
     * @param string               $name
     * @param string|callable|null $attribute
     * @param callable|null        $resolveCallback
     *
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct(...func_get_args());
    }

    /**
     * Set the options for the select menu.
     *
     * @param array|\Closure $options
     *
     * @return $this
     */
    public function options($options)
    {
        if( is_callable($options) ) {
            $options = call_user_func($options);
        }

        return $this->withMeta([
                                   'options' => collect($options ?? [])->map(function($label, $value) {
                                       return is_array($label) ? $label + [ 'value' => $value ] : [ 'label' => $label, 'value' => $value ];
                                   })->values()->all(),
                               ]);
    }

    /**
     * Allow inline select to auto-update field value on change on detail view.
     *
     * @return $this
     */
    public function disableTwoStepOnDetail()
    {
        return $this->withMeta([ 'detailTwoStepDisabled' => true ]);
    }

    /**
     * Allow inline select to auto-update field value on change on index view.
     *
     * @return $this
     */
    public function disableTwoStepOnIndex()
    {
        return $this->withMeta([ 'indexTwoStepDisabled' => true ]);
    }

    /**
     * Allow inline select to auto-update field value on change on index view.
     *
     * @return $this
     */
    public function disableTwoStepOnLens()
    {
        return $this->disableTwoStepOnIndex();
    }

    /**
     * Display values using their corresponding specified labels.
     *
     * @return $this
     */
    public function displayUsingLabels()
    {
        return $this->withMeta([ 'displayUsingLabels' => true ]);
    }

    /**
     * Enable inline editing on detail view.
     *
     * @return $this
     */
    public function inlineOnDetail()
    {
        return $this->withMeta([ 'inlineDetail' => true ]);
    }

    /**
     * Enable inline editing on index view.
     *
     * @return $this
     */
    public function inlineOnIndex()
    {
        return $this->withMeta([ 'inlineIndex' => true ]);
    }

    /**
     * Enable inline editing on index view.
     *
     * @return $this
     */
    public function inlineOnLens()
    {
        return $this->inlineOnIndex();
    }

    /**
     * Create a new field.
     *
     * @param string|array         $name
     * @param string|callable|null $attribute
     * @param callable|null        $resolveCallback
     *
     * @return self
     */
    public static function customized(
        $name,
        $attribute = null,
        ?callable $resolveCallback = null,
        $options = null,
        $default = null,
        $rules = 'required',
        bool $inlineOnIndex = true,
        bool $sortable = true
    ) {
        if( func_num_args() === 1 && is_array($name) ) {
            [
                'name' => $name,
                'attribute' => $attribute,
                'resolveCallback' => $resolveCallback,
                'options' => $options,
                'default' => $default,
                'sortable' => $sortable,
                'inlineOnIndex' => $inlineOnIndex,
                'rules' => $rules,
            ] = array_merge([
                                'name' => null,
                                'attribute' => null,
                                'resolveCallback' => null,
                                'options' => null,
                                'default' => null,
                                'sortable' => true,
                                'inlineOnIndex' => true,
                                'rules' => 'required',
                            ], $name);
        }

        $element = static::make($name, $attribute, $resolveCallback)
                         ->sortable((bool) $sortable);
        $options && $element->options($options);
        $default && $element->default($default);
        $rules && $element->rules($rules);
        $inlineOnIndex && $element->inlineOnIndex();

        return $element;
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        /** @var \Laravel\Nova\Resource $resource */
        $resource = Nova::resourceForModel($this->resource);
        $resourceUriKey = null;
        if( $resource && is_subclass_of($resource, \Laravel\Nova\Resource::class) ) {
            $resourceUriKey = $resource::uriKey() ?: null;
        }
        $resourceUriKey ??= getClassUriKey(get_class($this->resource));

        return array_merge(parent::jsonSerialize(), compact('resourceUriKey'));
    }
}
