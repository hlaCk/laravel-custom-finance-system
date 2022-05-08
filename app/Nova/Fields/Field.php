<?php

namespace App\Nova\Fields;

/**
 * Dynamic field class
 *
 * @method \Laravel\Nova\Fields\Select Select($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Text Text($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Number Number($name, $attribute = null, $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Textarea Textarea($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Date Date($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\DateTime DateTime($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Boolean Boolean($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\Currency Currency($name, $attribute = null, callable $resolveCallback = null)
 * @method \Laravel\Nova\Fields\HasMany HasMany($name, $attribute = null, $resource = null)
 * @method \Laravel\Nova\Fields\BelongsTo BelongsTo($name, $attribute = null, $resource = null)
 * @method \Laravel\Nova\Fields\BelongsToMany BelongsToMany($name, $attribute = null, $resource = null)
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
        throw_unless(
            class_exists($field = "\\Laravel\\Nova\\Fields\\{$field_name}"),
            new \Exception("Class {$field} not exists!")
        );
        $rest_arguments = [];

        if( count($arguments) === 2 ) {
            $rest_arguments = $arguments;
            if(
                $arguments[ 0 ] === $arguments[ 1 ] ||
                !is_callable($rest_arguments[ 1 ]) ||
                !is_string($rest_arguments[ 1 ]) ||
                !class_exists($rest_arguments[ 1 ])
            ) {
                unset($rest_arguments[ 0 ]);
            }

        } else {
            if( count($arguments) > 2 ) {
                $rest_arguments = array_slice($arguments, 1, count($arguments) - 1, true);
            } else {
                $rest_arguments = $arguments;
            }
        }

        $rest_arguments = count($arguments) === 1 ? [ ...$rest_arguments, null ] : $rest_arguments;
        $field_name = head($arguments);
        $arguments = is_string($field_name) ?
            [
                currentNovaResourceModelClass(fn($model) => $model::trans($field_name)),
                ...$rest_arguments,
            ] : $arguments;

        try {

            $field_model = new $field(...$arguments);
        } catch( \Exception $exception ) {
            dE(compact('arguments', 'field', 'exception'));
        }
        if( static::$sortable ) {
            /** @var \Laravel\Nova\Fields\Field $field_model */
            $field_model->sortable();
        }

        return $field_model;
    }
}
