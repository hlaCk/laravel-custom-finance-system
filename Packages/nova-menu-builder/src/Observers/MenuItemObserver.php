<?php

namespace OptimistDigital\MenuBuilder\Observers;

use OptimistDigital\MenuBuilder\MenuBuilder;
use OptimistDigital\MenuBuilder\Models\MenuItem;

/**
 *
 */
class MenuItemObserver
{
    /**
     * Handle the Cup "created" event.
     *
     * @param MenuItem $model
     *
     * @return void
     */
    public function creating(MenuItem $model)
    {
        $model->name = $model->name ?: uniqid() . '-' . (MenuItem::max($model->getKeyName()) + 1);
    }

    /**
     * Handle the Cup "created" event.
     *
     * @param MenuItem $model
     *
     * @return void
     */
    public function created(MenuItem $model)
    {
        // logger(['old'=>array_except($model->toArray(), ['fields'])]);
        if( !$model->OtherLocale(1)->count() ) {
            if( count($new_locales = array_except(MenuBuilder::getLocales(), [ $model->locale ])) ) {
                $new_model = $model->replicate([ 'id', ]);
                $new_model->locale = $new_locale = key($new_locales);
                $new_parent = $model->parent_id ? MenuItem::find($model->parent_id) : null;
                $new_parent = $new_parent ? $new_parent->OtherLocale(1)->first() : null;
                $new_model->parent_id = $new_parent ? $new_parent->id : null;
                $new_model->save();
            }
        }
    }

    /**
     * Handle the Cup "deleting" event.
     *
     * @param MenuItem $model
     *
     * @return void
     */
    public function deleted(MenuItem $model)
    {
        if( $other_locale = $model->OtherLocale(1)->get()->first() ) {
            return $other_locale->delete();
        }
    }
}
