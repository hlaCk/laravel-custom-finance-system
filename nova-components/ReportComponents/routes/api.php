<?php

use Illuminate\Support\Facades\Route;
use Sheets\ReportComponents\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::prefix('projects')->group(function () {
    Route::get('', [ ProjectController::class, 'index' ])
         ->name('ReportComponents::get-projects');
});
