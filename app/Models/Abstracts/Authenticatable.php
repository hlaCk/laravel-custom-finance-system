<?php

namespace App\Models\Abstracts;

use App\Traits\TModelTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Authenticatable extends \Illuminate\Foundation\Auth\User
{
    use HasFactory;
    use TModelTranslation;

    /**
     * @param string|array|\Closure                  $label
     * @param \Laravel\Nova\Resource|string|\Closure $title
     * @param \Closure|null|mixed                    $default
     * @param string|\Closure                        $title_key
     *
     * @return string|mixed
     */
    public static function getCustomTitle($label = 'titles', $title = '', $default = null, $title_key = 'details_title_template')
    {
        $label = value($label);
        if( func_num_args() === 1 && is_array($label) ) {
            $title = data_get($label, 'title', '');
            $default = data_get($label, 'default', null);
            $title_key = data_get($label, 'title_key', 'details_title_template');
            $label = data_get($label, 'label', 'titles');

            return static::getCustomTitle($label, $title, $default, $title_key);
        }

        $default = value($default);
        $attrs = implode('.', array_filter((array) $label, fn($v) => !empty($v) || is_numeric($v)));
        $translation = static::trans($attrs);
        $resource_label = $translation === $attrs ? $default : $translation;
        $title_key = (string) value($title_key);
        $title = value($title);
        $title = $title instanceof \Laravel\Nova\Resource ? $title->title() : $title;

        $result = static::trans($title_key, [
            'resource' => $resource_label,
            'title' => (string) $title,
        ]);

        return $result === $title_key ? $default : $result;
    }
}
