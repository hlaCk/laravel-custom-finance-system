<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class NovaQuickSearchController extends ResourceAttachController
{
    /**
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getOptions(NovaRequest $request)
    {
        $locales = array_keys((array) config('nova.locales'));
        $unregistered = toCollect(config('navigation.others', []));
        $options = collect(Nova::groupedResourcesForNavigation($request))
            ->map(function($resources, $group) use ($locales,&$unregistered) {
                $unregistered_grouped = data_get($unregistered, $group, []);
                /** @var \Illuminate\Support\Collection $resources */
                $resources = toCollect($resources)->merge($unregistered_grouped)->map(function($resource) use ($locales, $group) {
                    /** @var \App\Nova\Abstracts\Resource $resource */
                    $locale = str_replace('_', '-', app()->getLocale());
                    $locale = current(explode("-", $locale));
                    $app_locale = $locale ?: "";
                    $value = collect();
                    foreach( $locales as $locale ) {
                        app()->setLocale($locale);
                        $added_value = is_array($resource) ? __(data_get($resource, 'group')) . ": " . __(data_get($resource, 'navigationLabel')) : null;
                        $added_value ??= (($_group = $resource::group()) ? "{$_group}: " : "") . $resource::navigationLabel();
                        $value->add($added_value);
                    }
                    app()->setLocale($app_locale);

                    $label = $value->filter()->implode(' | ');
                    $value = is_array($resource) ? data_get($resource, 'uriKey') : $resource::uriKey();

                    return compact('label', 'value');
                })->filter();

                return $resources;
            })
            ->collapse();
        $push_url = '/resources/';
        $base_url = url(Nova::path() . $push_url);

        return response()->json([
                                    'data' => compact('options', 'base_url', 'push_url'),
                                    'success' => true,
                                ]);
    }
}
