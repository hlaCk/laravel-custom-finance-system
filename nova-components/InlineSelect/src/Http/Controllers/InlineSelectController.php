<?php

namespace Codi\InlineSelect\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class InlineSelectController extends Controller
{

    public function updateOrder(UpdateResourceRequest $request)
    {
        $success = DB::transaction(function() use (&$request) {
            $model = $request->findModelQuery()->lockForUpdate()->firstOrFail();

            return method_exists($model, 'exchangeOrder') ?
                $model->exchangeOrder($request->order) :
                $model->update([ 'order' => $request->order ]);
        });

        return response()->json(compact('success'));
    }
}
