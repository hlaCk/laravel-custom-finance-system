<?php

namespace Laravel\Nova\Fields;

class Hidden extends Text
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'hidden-field';

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
        parent::__construct($name, $attribute, $resolveCallback);

        $this->onlyOnForms();
    }

    public static function input($name, $value)
    {
        return static::make($name = value($name), $name)
                     ->nullable()
                     ->withMeta([ 'value' => value($value) ]);
    }
}
