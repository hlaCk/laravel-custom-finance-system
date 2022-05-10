<?php

namespace App\Nova\Fields;

use App\Nova\Abstracts\Resource;
use Laravel\Nova\Fields\Select;

/**
 *
 */
class StatusSelect extends Select
{
    /**
     * The displayable name of the field.
     *
     * @var string
     */
    public $name = 'status';

    /**
     * The attribute / column name of the field.
     *
     * @var string
     */
    public $attribute = 'status';

    /**
     * Create a new field.
     *
     * @param string|null          $name
     * @param string|callable|null $attribute
     * @param callable|null        $resolveCallback
     *
     * @return void
     */
    public function __construct($name = null, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name ?? $this->name, $attribute ?? $this->attribute, $resolveCallback);
    }

    /**
     * Make new field for the given resource.
     *
     * @param \App\Nova\Abstracts\Resource|null $resource
     *
     * @return self
     */
    public static function forResource(?Resource $resource = null): self
    {
        $model = !is_null($resource)
            ?
            resourceModelExtractor($resource)
            :
            currentNovaResourceModelClass();

        return static::make()
                     ->setResource($resource ?? currentNovaResourceClassCalled() ?? currentNovaResourceClass())
                     ->transName($model)
                     ->options($model::getAllStatuses())
                     ->default(fn($r) => $r->editing ? $model::getDefaultStatus() : null)
                     ->sortable()
                     ->displayUsing(fn($a) => $model::getStatusLabel($a));
    }

    /**
     * returns translated name.
     *
     * @param \App\Models\Abstracts\Model|string|null $model
     *
     * @return $this
     */
    public function transName($model = null): self
    {
        if( is_null($model) && ($resource = $this->resource) ) {
            $model = !is_null($resource)
                ?
                resourceModelExtractor($resource)
                :
                currentNovaResourceModelClass();
        }

        if( $model && class_exists($model) && is_callable([ $model, 'trans' ]) ) {
            $this->name = (string) $model::trans($this->name);
            return $this;
        }

        $request = getNovaRequest();
        $model =
            resourceModelExtractor(
                $request->resource ? $request->resource() : currentNovaResourceClassCalled()
            );
        if( class_exists($model) && is_callable([ $model, 'trans' ]) ) {
            $this->name = (string) $model::trans($this->name);
            return $this;
        }
//        $this->name = $this->resource ?? currentNovaResourceModelClass(fn($model) => $model::trans($this->name));

        return $this;
    }

    /**
     * @param Resource|string $resource
     *
     * @return $this
     */
    public function setResource($resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
