<?php

use Illuminate\Support\Facades\Route;

Route::prefix('entry_category')
     ->group(function () {
         Route::get(
             'only_has_contractors',
             [ \App\Http\Controllers\Api\Info\EntryCategoryController::class, 'only_has_contractors' ]
         );
         Route::get(
             'only_has_contractors/ids',
             [ \App\Http\Controllers\Api\Info\EntryCategoryController::class, 'only_has_contractors_ids' ]
         );
         Route::get(
             'has_contractors/{entry_category}',
             [ \App\Http\Controllers\Api\Info\EntryCategoryController::class, 'has_contractors' ]
         );
     });
