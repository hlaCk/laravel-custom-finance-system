<?php

use Illuminate\Support\Facades\Route;

Route::prefix('options')
     ->group(function () {
         Route::prefix('entry_category')
              ->group(function () {
                  Route::get(
                      'only_has_contractors',
                      [ \App\Http\Controllers\Api\Info\EntryCategoryController::class, 'options' ]
                  );
                  Route::get(
                      '{?entry_category}',
                      [
                          \App\Http\Controllers\Api\Info\EntryCategoryController::class,
                          'only_has_contractors_options',
                      ]
                  );
              });
     });
