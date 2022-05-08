<?php

namespace App\Nova\Fields;

/**
 * @alias for:
 *        \Laravel\Nova\Fields\Text::make(static::$model::trans('name'), 'name')->sortable()->showOnRelationships()
 */
class Name extends \Laravel\Nova\Fields\Text
{
    public function __construct($name = 'name', $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct(
            $name,
            $attribute,
            $resolveCallback
        );
        $this->sortable()
             ->showOnRelationships();

        if( $this->name === $this->attribute ) {
            if( ($calledClass = currentNovaResourceClassCalled()) && class_exists($calledClass) && is_callable(
                    $calledClass = [ $calledClass, 'trans' ]
                ) ) {
                $this->name = call_user_func($calledClass, $name);
            }
        }
    }
}
