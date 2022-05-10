<?php

namespace Epartment\NovaDependencyContainer;

use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Str;
use Iterator;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaDependencyContainer extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-dependency-container';

    /**
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * NovaDependencyContainer constructor.
     *
     * @param      $fields
     * @param null $attribute
     * @param null $resolveCallback
     */
    public function __construct($fields, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($attribute, $attribute, $resolveCallback);

        $this->withMeta([ 'fields' => $fields ]);
        $this->withMeta([ 'fallback_fields' => [] ]);
        $this->withMeta([ 'dependencies' => [] ]);
    }

    /**
     * @param string|\Closure $dependsOnField
     * @param array           $data = [
     *                              [$dependsOnValue, $elements]
     *                              ]
     *
     * @return \Illuminate\Http\Resources\MergeValue
     */
    public static function multiDependsOn($dependsOnField, array $data)
    {
        $fields = [];
        $dependsOnField = value($dependsOnField);
        foreach( $data as $field ) {
            [ $dependsOnValue, $elements ] = $field;
            $fields[] = static::make(array_wrap($elements))
                              ->dependsOn($dependsOnField, $dependsOnValue);
        }

        return new MergeValue($fields);
    }

    public static function forEachValue($value, \Closure $callback)
    {
        if(
            is_array($value = value($value)) ||
            $value instanceof iterator ||
            $value instanceof \Traversable ||
            $value instanceof \Illuminate\Database\Eloquent\Builder
        ) {
            $value = $value instanceof \Illuminate\Database\Eloquent\Builder ? $value->pluck('id') : $value;
            $value = $value instanceof iterator ? iterator_to_array($value) : $value;
            foreach( $value as $index => $item ) {
                $callback($item, $index, $value);
            }

            return true;
        }

        return false;
    }

    /**
     * Get layout for a specified field. Dot notation will result in {field}.{property}. If no dot was found it will
     * result in {field}.{field}, as it was in previous versions by default.
     *
     * @param $field
     * @param $value
     *
     * @return array
     */
    protected function getFieldLayout($field, $value = null)
    {
        if( count(($field = explode('.', $field))) === 1 ) {
            // backwards compatibility, property becomes field
            $field[ 1 ] = $field[ 0 ];
        }
        return [
            // literal form input name
            'field'    => $field[ 0 ],
            // property to compare
            'property' => $field[ 1 ],
            // value to compare
            'value'    => $value,
        ];
    }

    /**
     * @param string|\Closure                                                                  $dependsOnField
     * @param array|iterator|\Traversable|\Illuminate\Database\Eloquent\Builder|\Closure|mixed $dependsOnValue
     * @param array|\Closure|mixed                                                             $elements
     * @param array|\Closure|mixed|null                                                        $elementsIfNot
     *
     * @return \Illuminate\Http\Resources\MergeValue
     */
    public static function multiDependsOnNot($dependsOnField, $dependsOnValue, $elements, $elementsIfNot = null)
    {
        $fields = [];
        $dependsOnField = value($dependsOnField);
        $dependsOnValue = value($dependsOnValue);
        $elements = value($elements);
        $elementsIfNot = value($elementsIfNot);

        $_dependsOnValue = collect();
        if( static::forEachValue($dependsOnValue, fn($item) => $_dependsOnValue->push($item)) ) {
            $dependsOnValue = $_dependsOnValue->all();
        }

        $fields[] = static::make(array_wrap($elements))
                          ->dependsOn($dependsOnField, $dependsOnValue);

        if( !empty($elementsIfNot) ) {
            $fields[] = static::make(array_wrap($elementsIfNot))
                              ->dependsOnNot($dependsOnField, $dependsOnValue);
        }

        return new MergeValue($fields);
    }

    /**
     * Adds a dependency
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function dependsOn($field, $value)
    {
        if( static::forEachValue($value, fn($item) => $this->dependsOn($field, $item)) ) {
            return $this;
        }

        return $this->withMeta([
                                   'dependencies' => array_merge($this->meta[ 'dependencies' ], [
                                       $this->getFieldLayout($field, $value),
                                   ]),
                               ]);
    }


    /**
     * Adds a dependency else
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function dependsOnElse($field, $value)
    {
        if( static::forEachValue($value, fn($item) => $this->dependsOn($field, $item)) ) {
            return $this;
        }

        return $this->withMeta([
                                   'dependencies' => array_merge($this->meta[ 'dependencies' ], [
                                       $this->getFieldLayout($field, $value),
                                   ]),
                               ]);
    }

    /**
     * Adds a dependency for not
     *
     * @param $field
     *
     * @return NovaDependencyContainer
     */
    public function dependsOnNot($field, $value)
    {
        if( static::forEachValue($value, fn($item, $i,$v) => $this->dependsOnNot($field, $item)) ) {
            return $this;
        }

        return $this->withMeta([
                                   'dependencies' => array_merge($this->meta[ 'dependencies' ], [
                                       array_merge($this->getFieldLayout($field), [ 'not' => $value ]),
                                   ]),
                               ]);
    }

    /**
     * Adds a dependency for not empty
     *
     * @param $field
     *
     * @return NovaDependencyContainer
     */
    public function dependsOnEmpty($field)
    {
        return $this->withMeta([
                                   'dependencies' => array_merge($this->meta[ 'dependencies' ], [
                                       array_merge($this->getFieldLayout($field), [ 'empty' => true ]),
                                   ]),
                               ]);
    }

    /**
     * Adds a dependency for not empty
     *
     * @param $field
     *
     * @return NovaDependencyContainer
     */
    public function dependsOnNotEmpty($field)
    {
        return $this->withMeta([
                                   'dependencies' => array_merge($this->meta[ 'dependencies' ], [
                                       array_merge($this->getFieldLayout($field), [ 'notEmpty' => true ]),
                                   ]),
                               ]);
    }

    /**
     * Adds a dependency for null or zero (0)
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function dependsOnNullOrZero($field)
    {
        return $this->withMeta([
                                   'dependencies' => array_merge($this->meta[ 'dependencies' ], [
                                       array_merge($this->getFieldLayout($field), [ 'nullOrZero' => true ]),
                                   ]),
                               ]);
    }

    /**
     * Forward fillInto request for each field in this container
     *
     * @trace fill/fillForAction -> fillInto -> *
     *
     * @param NovaRequest $request
     * @param             $model
     * @param             $attribute
     * @param null        $requestAttribute
     */
    public function fillInto(NovaRequest $request, $model, $attribute, $requestAttribute = null)
    {
        foreach( $this->meta[ 'fields' ] as $field ) {
            $field->fill($request, $model);
        }
    }

    /**
     * Get the creation rules for this field.
     *
     * @param NovaRequest $request
     *
     * @return array|string
     */
    public function getCreationRules(NovaRequest $request)
    {
        $fieldsRules = $this->getSituationalRulesSet($request, 'creationRules');

        return array_merge_recursive(
            $this->getRules($request),
            $fieldsRules
        );
    }

    /**
     * Get a rule set based on field property name
     *
     * @param NovaRequest $request
     * @param string      $propertyName
     *
     * @return array
     */
    protected function getSituationalRulesSet(NovaRequest $request, string $propertyName = 'rules')
    {
        $fieldsRules = [ $this->attribute => [] ];
        if( !$this->areDependenciesSatisfied($request)
            || !isset($this->meta[ 'fields' ])
            || !is_array($this->meta[ 'fields' ]) ) {
            return $fieldsRules;
        }

        /** @var Field $field */
        foreach( $this->meta[ 'fields' ] as $field ) {
            $fieldsRules[ $field->attribute ] = is_callable($field->{$propertyName})
                ? call_user_func($field->{$propertyName}, $request)
                : $field->{$propertyName};
        }

        return $fieldsRules;
    }

    /**
     * Checks whether to add validation rules
     *
     * @param NovaRequest $request
     *
     * @return bool
     */
    public function areDependenciesSatisfied(NovaRequest $request)
    {
        if( !isset($this->meta[ 'dependencies' ])
            || !is_array($this->meta[ 'dependencies' ]) ) {
            return false;
        }

        $satisfiedCounts = 0;
        foreach( $this->meta[ 'dependencies' ] as $index => $dependency ) {

            if( array_key_exists('empty', $dependency) && empty($request->has($dependency[ 'property' ])) ) {
                $satisfiedCounts++;
            }

            if( array_key_exists('notEmpty', $dependency) && !empty($request->has($dependency[ 'property' ])) ) {
                $satisfiedCounts++;
            }

            // inverted
            if( array_key_exists('nullOrZero', $dependency) && in_array(
                    $request->get($dependency[ 'property' ]),
                    [ null, 0, '0' ],
                    true
                ) ) {
                $satisfiedCounts++;
            }

//            if( array_key_exists('not', $dependency) ) {
//                dE(
//                    $dependency[ 'not' ],
//                    $dependency[ 'property' ],
//                    $request->get($dependency[ 'property' ]),
//                    $request->all()
//                );
//            }

            if( array_key_exists('not', $dependency) && $dependency[ 'not' ] != $request->get(
                    $dependency[ 'property' ]
                ) ) {
                $satisfiedCounts++;
            }

            if( array_key_exists('value', $dependency) && $dependency[ 'value' ] == $request->get(
                    $dependency[ 'property' ]
                ) ) {
                $satisfiedCounts++;
            }
        }

        return $satisfiedCounts == count($this->meta[ 'dependencies' ]);
    }

    /**
     * Get the validation rules for this field.
     *
     * @param NovaRequest $request
     *
     * @return array
     */
    public function getRules(NovaRequest $request)
    {
        return $this->getSituationalRulesSet($request);
    }

    /**
     * Get the update rules for this field.
     *
     * @param NovaRequest $request
     *
     * @return array
     */
    public function getUpdateRules(NovaRequest $request)
    {
        $fieldsRules = $this->getSituationalRulesSet($request, 'updateRules');

        return array_merge_recursive(
            $this->getRules($request),
            $fieldsRules
        );
    }

    /**
     * Resolve dependency fields
     *
     * @param mixed  $resource
     * @param string $attribute
     *
     * @return array|mixed
     */
    public function resolve($resource, $attribute = null)
    {
        foreach( $this->meta[ 'fields' ] as $field ) {
            $field->resolve($resource, $attribute);
        }
    }

    /**
     * Resolve dependency fields for display
     *
     * @param mixed $resource
     * @param null  $attribute
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        foreach( $this->meta[ 'fields' ] as $field ) {
            $field->resolveForDisplay($resource);
        }

        foreach( $this->meta[ 'dependencies' ] as $index => $dependency ) {

            $this->meta[ 'dependencies' ][ $index ][ 'satisfied' ] = false;

            if( array_key_exists('empty', $dependency) && empty($resource->{$dependency[ 'property' ]}) ) {
                $this->meta[ 'dependencies' ][ $index ][ 'satisfied' ] = true;
                continue;
            }
            // inverted `empty()`
            if( array_key_exists('notEmpty', $dependency) && !empty($resource->{$dependency[ 'property' ]}) ) {
                $this->meta[ 'dependencies' ][ $index ][ 'satisfied' ] = true;
                continue;
            }
            // inverted
            if( array_key_exists('nullOrZero', $dependency) && in_array(
                    $resource->{$dependency[ 'property' ]},
                    [ null, 0, '0' ],
                    true
                ) ) {
                $this->meta[ 'dependencies' ][ $index ][ 'satisfied' ] = true;
                continue;
            }

            if( array_key_exists(
                    'not',
                    $dependency
                ) && $resource->{$dependency[ 'property' ]} != $dependency[ 'not' ] ) {
                $this->meta[ 'dependencies' ][ $index ][ 'satisfied' ] = true;
                continue;
            }

            if( array_key_exists('value', $dependency) ) {
                if( $dependency[ 'value' ] == $resource->{$dependency[ 'property' ]} ) {
                    $this->meta[ 'dependencies' ][ $index ][ 'satisfied' ] = true;
                    continue;
                }
                // @todo: quickfix for MorphTo
                $morphable_attribute = $resource->getAttribute($dependency[ 'property' ] . '_type');
                if( $morphable_attribute !== null && Str::endsWith(
                        $morphable_attribute,
                        '\\' . $dependency[ 'value' ]
                    ) ) {
                    $this->meta[ 'dependencies' ][ $index ][ 'satisfied' ] = true;
                    continue;
                }
            }

        }
    }
}
