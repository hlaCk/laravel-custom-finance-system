<?php

namespace App\Nova\Fields;

/**
 * Dynamic field class
 *
 * @method \Laravel\Nova\Fields\Select Select($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Text Text($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Textarea Textarea($name, $attribute = null, callable $resolveCallback = null)
 * @example Field::{CLASS NAME}(...arguments)
 */
class Field
{
    public static bool $sortable = true;

    /**
     * @param $field_name
     * @param $arguments
     *
     * @return \Laravel\Nova\Fields\Field
     * @throws \Throwable
     */
    public static function __callStatic($field_name, $arguments)
    {
        /** @var \App\Models\Abstracts\Model $model */
        /** @var \Laravel\Nova\Fields\Field|string $field */
        throw_unless(class_exists($field = "\\Laravel\\Nova\\Fields\\{$field_name}"), new \Exception("Class {$field} not exists!"));

        $arguments =
            count($arguments) === 1 &&
            ($field_name = head($arguments)) &&
            is_string($field_name) ?
                [
                    currentNovaResourceModelClass(fn($model) => $model::trans($field_name)),
                    $field_name,
                ] :
                $arguments;

        $field_model = new $field(...$arguments);
        if( static::$sortable ) {
            /** @var \Laravel\Nova\Fields\Field $field_model */
            $field_model->sortable();
        }

        return $field_model;
    }
}
