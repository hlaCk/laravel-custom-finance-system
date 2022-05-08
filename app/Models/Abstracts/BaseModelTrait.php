<?php

namespace App\Models\Abstracts;

use App\Traits\TModelTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin \App\Models\Abstracts\Model
 */
trait BaseModelTrait
{
    use HasFactory;
    use TModelTranslation;
    use \App\Traits\THasNextNewId;
    use \App\Traits\TInteractsWithMedia;

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

    /**
     * Get first record or create new instance.
     * Support translatable columns
     *
     * @param array|\Closure $columns
     * @param array|null     $locales
     * @param \Closure|null  $callback
     *
     * @return self
     */
    public static function firstOrMakeTranslatable($columns = [], ?array $locales = [ 'en' ], \Closure $callback = null): self
    {
        if( empty($columns) ) {
            return static::make();
        }

        $systemLocales = array_keys(config('nova.locales'));
        $callback ??= fn($v) => $v;
        $locales ??= $systemLocales;
        $instance = static::make();
        $query = static::query();

        if( is_array($columns) ) {
            foreach( $columns as $column => $value ) {
                $value = array_wrap($value);

                if( hasTrait($instance, \Spatie\Translatable\HasTranslations::class) && $instance->isTranslatableAttribute($column) ) {
                    $query->where(function($q) use ($value, $column, $locales) {
                        foreach( $locales as $locale ) {
                            $q->orWhere("{$column}->{$locale}", ...$value);
                        }
                    });
                } else {
                    $query->where($column, ...$value);
                }
            }
        } elseif( isClosure($columns) ) {
            $query = $columns($query, $locales, $callback);
        }

        if( !($model = $query->first()) ) {
            $model = static::make();
            if( is_array($columns) ) {
                $model = $model->setAllTranslations($columns);
            } elseif( isClosure($columns) ) {
                $model = $columns($model, $locales, $callback);
            }
        }

        return $callback($model);
    }

    /**
     * Get first record or create new one.
     * Support translatable columns
     *
     * @param array|\Closure $columns
     * @param array|null     $locales
     * @param \Closure|null  $callback
     *
     * @return self
     */
    public static function firstOrCreateTranslatable($columns = [], ?array $locales = [ 'en' ], \Closure $callback = null): self
    {
        return tap(static::firstOrMakeTranslatable($columns, $locales, $callback), function($model) {
            $model->save();
        });
    }

    /**
     * @param array|string $attributes
     * @param mixed        $value
     *
     * @return $this
     */
    public function setAllTranslations($attributes = [], $value = null, ?array $locales = null): self
    {
        if( empty($attributes) ) {
            return $this;
        }

        $attributes = !is_array($attributes) ? [ $attributes => $value ] : $attributes;
        foreach( (array) $attributes as $attribute => $_value ) {
            $this->setTranslations($attribute, localeWrap($_value, $locales));
        }

        return $this;
    }

    /**
     * Fill attributes after filter them.
     * Support translatable
     *
     * @param array                    $attributes
     * @param \Closure|array|bool|null $filter
     *
     * @return self
     */
    public function fillAttributes($attributes, $filter = null, ?array $locales = null)
    {
        $closure = is_null($filter) ? (fn($v) => true) : null;
        $closure ??= is_bool($filter) ? (fn($v) => $v) : null;
        $closure ??= is_array($filter) ? (fn($v) => !in_array($v, $filter)) : null;
        $closure ??= isClosure($filter) ? $filter : null;

        if( !is_null($closure) ) {
            $attributes = array_filter($attributes, $closure);
        }

        foreach( $attributes as $attribute => $value ) {
            /** @var \App\Models\Abstracts\Model $value */
            $value = isModel($value) ? $value->getKey() : $value;
            $value = hasTrait($this, \Spatie\Translatable\HasTranslations::class) && $this->isTranslatableAttribute($attribute) ? localeWrap($value, $locales) : $value;
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * Alias for toArray
     *
     * @return array
     */
    public function TA(): array
    {
        return $this->toArray();
    }

    /**
     * To disable/enable Foreign Key Constraints
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param bool                                  $disable
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoConstraints(Builder $builder, bool $disable = true)
    {
        $disable
            ? \Schema::disableForeignKeyConstraints()
            : \Schema::enableForeignKeyConstraints();

        return $builder;
    }
}
