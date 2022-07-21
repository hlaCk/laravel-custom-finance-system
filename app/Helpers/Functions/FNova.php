<?php

if( !function_exists('isRequestNovaIndex') ) {
    function isRequestNovaIndex(\Illuminate\Http\Request $request): bool
    {
        return $request instanceof \Laravel\Nova\Http\Requests\ResourceIndexRequest;
    }
}

if( !function_exists('isRequestNovaDetail') ) {
    function isRequestNovaDetail(\Illuminate\Http\Request $request): bool
    {
        return $request instanceof \Laravel\Nova\Http\Requests\ResourceDetailRequest;
    }
}

if( !function_exists('isRequestNovaCreate') ) {
    function isRequestNovaCreate(\Illuminate\Http\Request $request): bool
    {
        return $request instanceof \Laravel\Nova\Http\Requests\NovaRequest &&
               $request->editMode === 'create';
    }
}

if( !function_exists('isRequestNovaUpdate') ) {
    function isRequestNovaUpdate(\Illuminate\Http\Request $request): bool
    {
        return $request instanceof \Laravel\Nova\Http\Requests\NovaRequest &&
               $request->editMode === 'update';
    }
}

if( !function_exists('isRequestNovaAttach') ) {
    function isRequestNovaAttach(\Illuminate\Http\Request $request): bool
    {
        return $request->editing && in_array($request->editMode, [ 'attach', 'update-attached' ]);
    }
}

if( !function_exists('getNovaResourceId') ) {
    /**
     * @param \Illuminate\Http\Request|null $request
     *
     * @return int|double|string|mixed|null
     */
    function getNovaResourceId(\Illuminate\Http\Request $request = null)
    {
        $resourceId = ($request ??= request())->resourceId;
        if( is_numeric($resourceId) ) {
            $resourceId += 0;
        }

        return $resourceId;
    }
}

if( !function_exists('getNovaParentResource') ) {
    /**
     * returns the parent resource for which the current item is being created.
     *
     * @param \Illuminate\Http\Request|null $request
     *
     * @return string|null
     */
    function getNovaParentResource(\Illuminate\Http\Request $request = null): ?string
    {
        return ($request ??= request())->viaResource;
    }
}

if( !function_exists('getNovaParentResourceId') ) {
    /**
     * returns the parent resource id for which the current item is being created.
     *
     * @param \Illuminate\Http\Request|null $request
     *
     * @return int|double|string|mixed|null
     */
    function getNovaParentResourceId(\Illuminate\Http\Request $request = null)
    {
        $resourceId = ($request ??= request())->viaResourceId;
        if( is_numeric($resourceId) ) {
            $resourceId += 0;
        }

        return $resourceId;
    }
}

if( !function_exists('currentNovaResourceClassCalled') ) {
    /**
     * Get Nova Resource Class through debug backtrace.
     *
     * @param \Closure|null $callback
     *
     * @return string|\App\Nova\Abstracts\Resource|null
     */
    function currentNovaResourceClassCalled(\Closure $callback = null, ?array $resolveByKeyValue = null): ?string
    {
        $class = null;

        $resolveByKey = 'class';
        $resolveByValue = \Laravel\Nova\Resource::class;
        $defaultResolveByOffset = fn() => fn($index, $trace) => $index - 1;
        $resolveByOffset = $defaultResolveByOffset();

        $resolveByKeyValue =
            is_null($resolveByKeyValue) || !is_array($resolveByKeyValue) || !count($resolveByKeyValue)
                ? [
                $resolveByKey => $resolveByValue,
                'offset'      => $resolveByOffset,
            ]
                :
                $resolveByKeyValue;
        try {
            $resolveByKey = key($resolveByKeyValue);
            $resolveByValue = head($resolveByKeyValue);
            $resolveByOffset = data_get($resolveByKeyValue, 'offset', $defaultResolveByOffset);
        } catch( Exception $exception ) {
            $resolveByKey = 'class';
            $resolveByValue = \Laravel\Nova\Resource::class;
            $resolveByOffset = $defaultResolveByOffset();
        }
        $resolveByKey = trim(value($resolveByKey));
        $resolveByValue = trim(value($resolveByValue));
        $resolveByOffset = isClosure($resolveByOffset) ? $resolveByOffset : fn($index, $trace) => $resolveByValue;

        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach( $debug as $index => $item ) {
            if( isset($item[ $resolveByKey ]) && $item[ $resolveByKey ] == $resolveByValue ) {
                $index = (double) ($resolveByOffset($index, $item) ?? $index - 1);
                $class = data_get($debug, ($index) . ".class");
                if( is_null($class) || in_array($class, [
                        'Laravel\Nova\Resource',
                        'App\Nova\Abstracts\Resource',
                    ]) ) {
                    $_debug = array_reverse(slice($debug, 0, $index));
                    foreach( $_debug as $_index => $_item ) {
                        if(
                            isset($_item[ $resolveByKey ]) &&
                            is_subclass_of($_item[ $resolveByKey ], \Laravel\Nova\Resource::class)
                        ) {
                            $class = $_item[ $resolveByKey ];
                            break;
                        }
                    }
                }
                break;
            }
        }

        if( is_null($class) && class_exists($resolveByValue) ) {
            foreach( $debug as $index => $item ) {
                if( isset($item[ $resolveByKey ]) && (
                        is_subclass_of($item[ $resolveByKey ], $resolveByValue) ||
                        is_a($item[ $resolveByKey ], $resolveByValue)
                    ) ) {
                    $index = (double) ($resolveByOffset($index, $item) ?? $index - 1);
                    $class = data_get($debug, ($index) . ".class");
                    break;
                }
            }
        }

        return with($class, $callback ?? fn($model) => $model);
    }
}

if( !function_exists('currentNovaResourceClass') ) {
    /**
     * Get Nova Resource Class through request debug backtrace.
     *
     * @param \Closure|null $callback
     *
     * @return string|\App\Nova\Abstracts\Resource|null
     */
    function currentNovaResourceClass(\Closure $callback = null): ?string
    {
//        if( getNovaRequest()->viaResource() ) {
        return with(getNovaResource(), $callback ?? fn($model) => $model);
//        }

    }
}

if( !function_exists('currentNovaResourceModelClass') ) {
    /**
     * Get Nova Resource Model Class through debug backtrace.
     *
     * @param \Closure|null $callback
     *
     * @return string|\App\Models\Abstracts\Model|null
     */
    function currentNovaResourceModelClass(\Closure $callback = null): ?string
    {
        return with(
            currentNovaResourceClassCalled(\Closure::fromCallable('resourceModelExtractor')),
            $callback ?? fn($model) => $model
        );
    }
}

if( !function_exists('resourceModelExtractor') ) {
    /**
     * Get Nova Resource Model Class through resource class.
     *
     * @param \App\Nova\Abstracts\Resource|string $resource
     *
     * @return string|\App\Models\Abstracts\Model|null
     */
    function resourceModelExtractor($resource)
    {
        return class_exists(
            is_object($resource) ? get_class($resource) : $resource
        ) ? ($resource::$model ?? null) : null;
    }
}

if( !function_exists('getNovaRequest') ) {
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Laravel\Nova\Http\Requests\NovaRequest|mixed
     */
    function getNovaRequest()
    {
        return app(\Laravel\Nova\Http\Requests\NovaRequest::class);
    }
}

if( !function_exists('getNovaResourceInfoFromRequest') ) {
    /**
     * @param \Illuminate\Http\Request|null $request
     * @param string|null                   $key
     *
     * @return array|string|null|mixed
     */
    function getNovaResourceInfoFromRequest(?\Illuminate\Http\Request $request = null, ?string $key = null)
    {
        $resourceInfo = [
            'resource'     => null,
            'resourceName' => null,
            'resourceId'   => null,
            'mode'         => null,
        ];

        try {
            $request ??= getNovaRequest();

            if( $request->segment(2) === 'resources' ) {
                $resourceInfo = [
//                    'resource'     => $request->resource()?: Nova::resourceForKey($resourceName = $request->segment(3)),
'resource'     => Nova::resourceForKey($resourceName = $request->segment(3)),
'resourceName' => $resourceName,
'resourceId'   => $resourceId = $request->segment(4),
'mode'         => $request->segment(5) ?? ($resourceId ? 'view' : 'index'),
                ];
            }
        } catch( Exception $exception ) {
        }

        return is_null($key) ? $resourceInfo : data_get($resourceInfo, $key);
    }
}

if( !function_exists('getNovaRequestParameters') ) {
    /**
     * @param \Illuminate\Http\Request|null $request
     * @param array|string|null             $key
     *
     * @return array|object|string|null|mixed
     */
    function getNovaRequestParameters(?\Illuminate\Http\Request $request = null, $key = null)
    {
        $results = [];
        try {
            $request ??= getNovaRequest();


            /** @var \Illuminate\Routing\Route $route */
            $route = call_user_func($request->getRouteResolver());

            if( is_null($route) ) {
                return $results;
            }
//dd(
//    $request->route()->hasParameter('resource'),
//   $route->hasParameter('resource'),
//   $request->hasParameter('resource')
//);
            $results = $route->parameters();

            if( is_null($key) ) {
                if( is_array($results) && isset($results[ 'resource' ]) ) {
                    $results[ 'resource_class' ] = Nova::resourceForKey($results[ 'resource' ]);
                    $results[ 'resource_model' ] = Nova::modelInstanceForKey($results[ 'resource' ]);
                    $results[ 'model' ] =
                        fn() => isset($results[ 'resourceId' ]) && isset($results[ 'resource_model' ]) && class_exists(
                            $results[ 'resource_model' ]
                        ) ?
                            $results[ 'resource_model' ]::find($results[ 'resourceId' ]) : null;
                }
            } else {
                $key = (array) $key;
                $results = blank($key) ? $results : array_only($results, $key);
            }

        } catch( Exception $exception ) {
        }

        return $results;
    }
}

if( !function_exists('getNovaResource') ) {
    /**
     * Get current browsing nova resource via link.
     *
     * @return string|null
     */
    function getNovaResource(): ?string
    {
        $request = getNovaRequest();
        $resource = $request->resource ? $request->resource() : null;
//dd($resource);
        try {
            $resource = $resource ?: getNovaResourceInfoFromRequest(null, 'resource');
//            $resource = getNovaResourceInfoFromRequest(null, 'resource');
        } catch( Exception $exception ) {
            $resource = null;
        }

        return $resource;
    }
}

if( !function_exists('isActiveNavigationItem') ) {
    /**
     * Check if the given resource is active now.
     *
     * @param array|\Illuminate\Contracts\Support\Arrayable|\Illuminate\Support\Collection $resources
     *
     * @return bool
     */
    function isActiveNavigationItem($resources): bool
    {
        $path = request()->path();
        $resource = getNovaResource();

        return toCollect($resources)
                   ->search(function ($value) use ($path, $resource) {
                       if( is_array($value) ) {
                           return isset($value[ 'uriKey' ]) && (
                                   $value[ 'uriKey' ] == $path || $value[ 'uriKey' ] == '/' . $path
                               );
                       } else {
                           if( is_string($value) ) {
                               return $value == $resource;
                           }
                       }

                       return $resource && array_search($resource, (array) $value, false);
                   }) !== false;
    }
}

if( !function_exists('isRequestRelationshipTypeHasMany') ) {
    /**
     * @param \Illuminate\Http\Request|null $request
     *
     * @return bool
     */
    function isRequestRelationshipTypeHasMany(?\Illuminate\Http\Request $request = null): bool
    {
        $request ??= getNovaRequest();
        return $request->relationshipType === 'hasMany';
    }
}

if( !function_exists('iisViaRelationship') ) {
    /**
     * determines if the request via relationship
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest|null $request
     *
     * @return bool
     */
    function isViaRelationship(?\Laravel\Nova\Http\Requests\NovaRequest $request = null): bool
    {
        $request ??= getNovaRequest();
        return ($request->editing && in_array($request->editMode, [ 'attach', 'update-attached' ])) ||
               $request->relationshipType === 'hasMany' ||
               $request->viaResource;
    }
}

if( !function_exists('isCurrentResource') ) {
    /**
     * Check if current resource is the given resource.
     *
     * @param string $resource
     *
     * @return bool
     */
    function isCurrentResource(string $resource): bool
    {
        $request = getNovaRequest();
        return (
                   $request->resource &&
                   $request->resource() === $resource
               ) || (
                   ($currentResource = request('view')) &&
                   class_exists($resource) &&
                   method_exists($resource, 'uriKey') &&
                   $currentResource === 'resources/' . $resource::uriKey()
               );
    }
}

if( !function_exists('getNovaResources') ) {
    /**
     * Get All nova resources with array of resources to exclude it.
     *
     * @param string              $app_dir
     * @param array|\Closure|null $except
     * @param string              $parent_class
     *
     * @return array
     */
    function getNovaResources(
        string $app_dir = 'Nova',
               $except = null,
        string $parent_class = \App\Nova\Abstracts\Resource::class
    ): array {
        $except = (array) value($except ?? \App\Providers\NovaServiceProvider::$skipLoadingResources ?? []);

        if( str_contains($app_dir, ',') ) {
            $resources = collect();
            foreach( explode(",", $app_dir) as $_dir ) {
                $resources->add(glob(app_path($_dir) . DIRECTORY_SEPARATOR . '*.php'));
            }

            $resources = $resources->collapse()->values()->all();
        } else {
            $resources = glob(app_path($app_dir) . DIRECTORY_SEPARATOR . '*.php');
        }

        return toCollect($resources)
            ->map(function ($resource) use ($parent_class) {
                $resource_class = str_ireplace(
                    [ "/", ".php" ],
                    [ "\\", "" ],
                    "App" . str_after($resource, app_path())
                );
                $is_nova_resource =
                    $resource_class !== $parent_class && class_exists($resource_class) && is_subclass_of(
                        $resource_class,
                        $parent_class
                    );

                return $is_nova_resource ? $resource_class : null;
            })
            ->filter(fn($r) => $r && !in_array($r, $except))
            ->values()
            ->toArray();
    }
}

if( !function_exists('getAllNovaResources') ) {
    /**
     * Get All nova resources.
     *
     * @param string $app_dir
     * @param string $parent_class
     *
     * @return array
     */
    function getNovaAllResources(
        string $app_dir = 'Nova',
        string $parent_class = \App\Nova\Abstracts\Resource::class
    ): array {
        if( str_contains($app_dir, ',') ) {
            $resources = collect();
            foreach( explode(",", $app_dir) as $_app_dir ) {
                $resources->add(glob(app_path($_app_dir) . DIRECTORY_SEPARATOR . '*.php'));
            }

            $resources = $resources->collapse()->values()->all();
        } else {
            $resources = glob(app_path($app_dir) . DIRECTORY_SEPARATOR . '*.php');
        }

        return toCollect($resources)
            ->map(function ($resource) use ($parent_class) {
                $resource_class = str_ireplace(
                    [ "/", ".php" ],
                    [ "\\", "" ],
                    "App" . str_after($resource, app_path())
                );
                $is_nova_resource =
                    $resource_class !== $parent_class && class_exists($resource_class) && is_subclass_of(
                        $resource_class,
                        $parent_class
                    );

                return $is_nova_resource ? $resource_class : null;
            })
            ->filter()
            ->values()
            ->toArray();
    }
}

if( !function_exists('getNovaResourcesAsOptions') ) {
    /**
     * Get All nova resources as options.
     *
     * @param string $app_dir
     * @param string $parent_class
     *
     * @return array
     */
    function getNovaResourcesAsOptions(
        string $app_dir = 'Nova',
        string $parent_class = \App\Nova\Abstracts\Resource::class
    ): array {
        return collect(getNovaResources($app_dir, null, $parent_class))
            ->filter(fn($f) => class_exists($f) && is_subclass_of($f, $parent_class))
            ->mapWithKeys(fn($r) => [
                /** @var \App\Nova\Abstracts\Resource $r */
                $r::singularLabel() => $r::singularLabel() . ' - ' . $r::label(),
            ])
            ->toArray();
    }
}

if( !function_exists('getNovaResourcesDependencies') ) {
    /**
     * Get All nova resources Dependencies.
     *
     * @param array|null $options
     *
     * @return array
     */
    function getNovaResourcesDependencies(
        ?string $field = null,
        ?array  $options = null
    ): array {
        $options = (array) ($options ?? getNovaResourcesAsOptions());
        $dependencies = [];

        foreach( $options as $option => $label ) {
            $n = "text";
            $dependencies[] = \Epartment\NovaDependencyContainer\NovaDependencyContainer::make([
                                                                                                   \Laravel\Nova\Fields\Text::make(
                                                                                                       ($text =
                                                                                                           "{$option} Text ") . $n,
                                                                                                       $n
                                                                                                   ),
                                                                                               ])->dependsOn(
                $field,
                $option
            );
        }

        return $dependencies;
    }
}

if( !function_exists('isLocaleAllowed') ) {
    /**
     * @param string|\Closure $locale
     *
     * @return bool
     */
    function isLocaleAllowed($locale): bool
    {
        return array_key_exists($locale, config('nova.locales'));
    }
}

if( !function_exists('getDefaultFromDate') ) {
    /**
     * @return \Carbon\Carbon|\Carbon\CarbonPeriod|\DateTime|\Illuminate\Support\Carbon
     */
    function getDefaultFromDate()
    {
        return now()->firstOfYear();
    }
}

if( !function_exists('getDefaultToDate') ) {
    /**
     * @return \Carbon\Carbon|\Carbon\CarbonPeriod|\DateTime|\Illuminate\Support\Carbon
     */
    function getDefaultToDate()
    {
        return now()->endOfYear();
    }
}

if( !function_exists('formatAttributeAsCurrency') ) {
    /**
     * @param string|\Closure                      $attribute
     * @param \App\Models\Abstracts\Model|\Closure $model
     * @param string|\Closure|null                 $locale
     *
     * @return string
     */
    function formatAttributeAsCurrency($attribute, $model, $locale = null)
    {
        $attribute = value($attribute);
        $model = value($model);
        $locale = value($locale, currentLocale());
        return \Laravel\Nova\Fields\Currency::make('Element', $attribute)
                                            ->formatMoney($model->$attribute, null, $locale);
    }
}

if( !function_exists('formatValueAsCurrency') ) {
    /**
     * @param string|\Closure      $value
     * @param string|\Closure|null $locale
     *
     * @return string
     */
    function formatValueAsCurrency($value, $locale = null)
    {
        $value = value($value);
        $locale = value($locale, currentLocale());
        $locale ??= config('nova.money_locale', currentLocale());

        return \Laravel\Nova\Fields\Currency::make('Element', $value)
                                            ->formatMoney($value, null, $locale);
    }
}

if( !function_exists('makeFormatValueAsCurrency') ) {
    /**
     * @return \Closure
     */
    function makeFormatValueAsCurrency(): Closure
    {
        return static fn($v, $l = null) => formatValueAsCurrency($v, $l);
    }
}

if( !function_exists('parseNovaFieldArguments') ) {
    /**
     * @param string               $name
     * @param string|callable|null $attribute
     * @param string|callable|null $resolveCallback
     *
     * @return array|\Closure
     */
    function parseNovaFieldArguments($name, $attribute = null, $resolveCallback = null): array
    {
        if( is_null($attribute) && is_null($resolveCallback) ) {
            $attribute = $name;
        } else {
            if( is_null($resolveCallback) ) {
                if( $attribute instanceof Closure ||
                    (is_callable($attribute) && is_object($attribute)) ||
                    class_exists($attribute) ) {
                    $resolveCallback = $attribute;
                    $attribute = $name;
                }
            }
        }
        return [ $name, $attribute, $resolveCallback ];
    }
}

if( !function_exists('whenCurrentResourceIs') ) {
    /**
     * if current resource equal to the given resource call $whenTrue|null else call $whenFalse|null
     *
     * @param string|\App\Nova\Abstracts\Resource $resource
     * @param callable|null|mixed                 $whenTrue
     * @param callable|null|mixed                 $whenFalse
     * @param mixed|null                          $with
     *
     * @return array|mixed
     */
    function whenCurrentResourceIs($resource, $whenTrue = [], $whenFalse = [], $with = null)
    {
        $resource = value($resource);
        $resource = !is_string($resource) ? get_class($resource) : $resource;

        return when(isCurrentResource($resource), $whenTrue, $whenFalse, $with);
    }
}

if( !function_exists('getNovaResourceByUriKey') ) {
    /**
     * Get the resource class name for a given key.
     *
     * @param string $key
     *
     * @return string
     */
    function getNovaResourceByUriKey($key)
    {
        return tap(\Laravel\Nova\Nova::resourceForKey($key), function ($resource) {
            abort_if(is_null($resource), 404);
        });
    }
}

//if( !function_exists('getNovaResourceModelQuery') ) {
    /**
     * Get the resource class name for a given key.
     *
     * @param string $key
     *
     * @return string
     */
//    function getNovaResourceModelQuery($key, $default = null)
//    {
//        /** @var \App\Nova\Abstracts\Resource $resource */
//        if( $resource = getNovaResourceByUriKey($key) ) {
//            $model = $resource::newModel();
//
//            $query = $model->newQueryWithoutScopes()
//                ->whereKey($resourceId ?? $this->resourceId)
//                ->find($this->viaResourceId) : null;
//
//        }
//        return $query
//    }
//}
