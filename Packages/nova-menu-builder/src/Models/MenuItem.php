<?php

namespace OptimistDigital\MenuBuilder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OptimistDigital\MenuBuilder\MenuBuilder;

/**
 *
 */
class MenuItem extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'menu_id',
        'name',
        'value',
        'class',
        'target',
        'parent_id',
        'order',
        'enabled',
        'data',
        'locale',
        // 'end_point'
    ];

    /**
     * @var string[]
     */
    protected $with = [ 'children' ];

    /**
     * @var string[]
     */
    protected $casts = [
        'enabled' => 'boolean',
        'data' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [ 'enabledClass', 'displayValue', 'fields', 'type' ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(MenuBuilder::getMenuItemsTableName());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuBuilder::getMenuClass());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuBuilder::getMenuItemClass(), 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->with('children')->orderBy('order');
    }

    /**
     * @param $parentId
     *
     * @return mixed
     */
    public function itemsChildren($parentId)
    {
        return $this->whereParentId($parentId);
    }

    /**
     * @return string
     */
    public function getEnabledClassAttribute(): string
    {
        return ($this->enabled) ? 'enabled' : 'disabled';
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param                                       $limit
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function scopeOtherLocale(Builder $query, $limit = null)
    {
        return $query
            ->where('locale', '!=', $this->locale)
            ->where('class', $this->class)
            ->where('name', $this->name)
            ->when(!is_null($limit), fn($q) => $q->limit((int) $limit))
            ->whereKeyNot($this->id);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * @param $query
     * @param $locale
     *
     * @return mixed
     */
    public function scopeByLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * @return mixed
     */
    public function getDisplayValueAttribute()
    {
        if( class_exists($this->class) ) {
            return $this->class::getDisplayValue($this->value, $this->data, $this->locale);
        }

        return $this->value;
    }

    /**
     * @return null
     */
    public function getTypeAttribute()
    {
        if( class_exists($this->class) ) {
            return $this->class::getIdentifier($this->value);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getCustomValueAttribute()
    {
        if( class_exists($this->class) ) {
            return $this->class::getValue($this->value, $this->data, $this->locale);
        }

        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getCustomDataAttribute()
    {
        if( class_exists($this->class) ) {
            return $this->class::getData($this->data);
        }

        return $this->data;
    }

    /**
     * @return array
     */
    public function getFieldsAttribute(): array
    {
        $fields = MenuBuilder::getFieldsFromMenuItemTypeClass($this->class);
        foreach( $fields as $field ) {
            $field->resolve($this);
        }

        return $fields;
    }
}
