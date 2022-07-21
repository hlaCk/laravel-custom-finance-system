<?php

use Illuminate\Support\Facades\Route;

Route::prefix('contractor')
     ->group(function () {
         Route::get('data/{project_id?}', [\App\Http\Controllers\Api\Info\ContractorController::class, 'fetchData']);
     });
