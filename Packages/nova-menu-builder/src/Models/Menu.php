<?php

namespace OptimistDigital\MenuBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use OptimistDigital\MenuBuilder\MenuBuilder;

class Menu extends Model
{
    protected $fillable = [ 'name', 'slug' ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(MenuBuilder::getMenusTableName());
    }

    public function rootMenuItems()
    {
        return $this
            ->hasMany(MenuBuilder::getMenuItemClass())
            ->where('parent_id', null)
            ->orderBy('parent_id')
            ->orderBy('order')
            ->orderBy('name');
    }

    public function menuItems(?string $locale = null)
    {
        $locale ??= currentLocale();

        return $this->rootMenuItems()
                    ->when($locale, fn($_query) => $_query->where('locale', $locale))
                    ->get();
    }

    public function formatForAPI($locale = null)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'locale' => $locale ?? $this->locale,
            'menuItems' => $this->rootMenuItems()
                                ->where('locale', $locale)
                                ->get()
                                ->map(function($menuItem) {
                                    return $this->formatMenuItem($menuItem);
                                }),
        ];
    }

    public function formatMenuItem($menuItem)
    {
        return [
            'id' => $menuItem->id,
            'name' => $menuItem->name,
            'type' => $menuItem->type,
            'value' => $menuItem->customValue,
            'target' => $menuItem->target,
            'enabled' => $menuItem->enabled,
            'data' => $menuItem->customData,
            // 'end_point' => $menuItem->end_point,
            'children' => toCollect($menuItem->children)->map(function($item) {
                return $this->formatMenuItem($item);
            }),
        ];
    }
}
