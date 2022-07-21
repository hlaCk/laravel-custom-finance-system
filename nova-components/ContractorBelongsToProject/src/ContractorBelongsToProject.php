<?php

namespace Info\Components;

use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Models\Info\Contractor\Contractor;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class ContractorBelongsToProject extends Select
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'contractor-belongs-to-project';
    /**
     * @var string|null
     */
    protected $entry_category_field;
    /**
     * @var string|null
     */
    protected $project_field;

    /**
     * Create a new field.
     *
     * @param string               $name
     * @param string|callable|null $attribute
     * @param null                 $entry_category_field
     * @param null                 $project_field
     * @param callable|null        $resolveCallback
     *
     */
    public function __construct(
        $name,
        $attribute = null,
        $entry_category_field = null,
        $project_field = null,
        callable $resolveCallback = null
    ) {
        parent::__construct($name, $attribute, $resolveCallback);
        $this->entry_category_field = $entry_category_field;
        $this->project_field = $project_field;

        $project = getNovaRequest()
            ->newQueryWithoutScopes()
            ->whereKey(getNovaResourceId())
            ->with(['project.contractors'])
            ->firstOrFail()
            ->project;
        $data = $project ? $project->contractors : Contractor::onlyActive()->latest('id');

        $this->nullable()
//             ->displayUsingLabels()
             ->options(
                 (array) SelectOptionsResource::collection($data)->toArray(getNovaRequest())
             );
    }

    /**
     * Set the options for the select menu.
     *
     * @param array|\Closure|\Illuminate\Support\Collection
     *
     * @return $this
     */
    public function options($options)
    {
        if( is_callable($options) ) {
            $options = $options();
        }

        return $this->withMeta([
                                   'options' => collect($options ?? [])->map(function ($label, $value) {
                                       if( isset($label[ 'group' ]) ) {
                                           return [
                                               'label' => $label[ 'group' ] . ' - ' . $label[ 'label' ],
                                               'value' => $value,
                                           ];
                                       }

                                       return is_array($label)
                                           ? $label + [ 'value' => $value ]
                                           : [
                                               'label' => $label,
                                               'value' => $value,
                                           ];
                                   })->values()->all(),

                                   //                                   'options2' => collect($options ?? [])->map(function($label, $value) {
                                   //                                       return is_array($label) ? $label + [ 'value' => $value ] : [ 'label' => $label, 'value' => $value ];
                                   //                                   })->values()->all(),
                               ]);
    }

    /**
     * Display values using their corresponding specified labels.
     *
     * @return $this
     */
    public function displayUsingLabels()
    {
        return $this->displayUsing(function ($value) {
            return collect($this->meta[ 'options' ])
                       ->where('value', $value)
                       ->first()[ 'label' ] ?? $value;
        });
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return with(app(NovaRequest::class), function ($request) {
            return array_merge(parent::jsonSerialize(), [
                'searchable' => is_bool($this->searchable)
                    ? $this->searchable
                    : call_user_func($this->searchable, $request),
            ],                 $this->parseFieldMeta());
        });
    }

    public function parseFieldMeta(): array
    {
        return [
            'entry_category_field' => $this->entry_category_field,
            'project_field'        => $this->project_field,
        ];
    }

    /**
     * Enable subtitles within the related search results.
     *
     * @return $this
     * @throws \Exception
     */
    public function withSubtitles()
    {
        throw new \Exception('The `withSubtitles` option is not available on Select fields.');
    }

    /**
     * Create a new field.
     *
     * @param string               $name
     * @param string|callable|null $attribute
     * @param callable|null        $resolveCallback
     *
     * @return static
     */
    public static function makee(
        $name,
        $attribute = null,
        $entry_category_field = null,
        $project_field = null,
        callable $resolveCallback = null
    ) {
        return static::make(...func_get_args());
    }

    /**
     * Set the options for the select menu.
     *
     * @param array|\Closure $options
     *
     * @return $this
     */
    public function options2($options)
    {
        if( is_callable($options) ) {
            $options = call_user_func($options);
        }

        return $this->withMeta([
                                   'options' => collect($options ?? [])->map(function ($label, $value) {
                                       return is_array($label)
                                           ? $label + [ 'value' => $value ]
                                           : [
                                               'label' => $label,
                                               'value' => $value,
                                           ];
                                   })->values()->all(),
                               ]);
    }

    /**
     * @return string|null
     */
    public function getEntryCategoryField()
    {
        return $this->entry_category_field;
    }

    /**
     * @param string|\Closure|null $entry_category_field
     *
     * @return $this
     */
    public function setEntryCategoryField($entry_category_field = null)
    {
        $this->entry_category_field = value($entry_category_field);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProjectField()
    {
        return $this->project_field;
    }

    /**
     * @param string|\Closure|null $project_field
     *
     * @return $this
     */
    public function setProjectField($project_field = null)
    {
        $this->project_field = value($project_field);

        return $this;
    }
}
