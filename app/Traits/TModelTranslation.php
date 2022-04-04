<?php

namespace App\Traits;

trait TModelTranslation
{
    /**
     * alias for __("models/model_name") and __("models/model_name.fields.field_name")
     *
     * @param string $key
     *
     * @return array|string|null
     */
    public static function trans(string $key, $replace = [], $locale = null)
    {
        if($path = str_ireplace('App\\Models\\','', dirname(static::class))) {
            $path = collect(explode('\\', $path))->map('snake_case')->implode('/');
            $path = $path ? str_finish("models/{$path}", '/') : "";
        } else {
            $path = "models/";
        }

        $model = snake_case(class_basename(static::class));

        return getTrans(
            "{$path}{$model}.{$key}",
            getTrans(
                "{$path}{$model}.fields.{$key}",
                $key,
                $replace,
                $locale
            ),
            $replace,
            $locale
        );
    }
}
