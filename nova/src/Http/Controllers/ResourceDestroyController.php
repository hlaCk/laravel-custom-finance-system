<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Http\Requests\DeleteResourceRequest;
use Laravel\Nova\Nova;

class ResourceDestroyController extends Controller
{
    use DeletesFields;

    /**
     * Destroy the given resource(s).
     *
     * @param \Laravel\Nova\Http\Requests\DeleteResourceRequest $request
     *
     * @return array|\Illuminate\Http\Response|string[]
     */
    public function handle(DeleteResourceRequest $request)
    {
        $messages = [];
        $request->chunks(150, function($models) use ($request, &$messages) {
            $models->each(function($model) use ($request, &$messages) {
                $this->deleteFields($request, $model);

                $uses = class_uses_recursive($model);

                if( in_array(Actionable::class, $uses) && !in_array(SoftDeletes::class, $uses) ) {
                    $model->actions()->delete();
                }
                try {
                    $model->delete();
                } catch(\Exception $exception) {
                    if( str_contains($exception->getMessage(), "constraint violation") ) {
                        $messages[] = $model;
                    } else {
                        throw $exception;
                    }
                }

                tap(Nova::actionEvent(), function($actionEvent) use ($model, $request) {
                    DB::connection($actionEvent->getConnectionName())->table('action_events')->insert(
                        $actionEvent->forResourceDelete($request->user(), collect([ $model ]))
                            ->map->getAttributes()->all()
                    );
                });
            });
        });

        if( !empty($messages) ) {
            return response()->json([
                                        'message' => __("Can not delete records with related data!"),
                                    ]);
        }

        if( $request->isForSingleResource() && !is_null($redirect = $request->resource()::redirectAfterDelete($request)) ) {
            return response()->json([
                                        'redirect' => $redirect,
                                    ]);
        }
    }
}
