<?php

use Illuminate\Support\Facades\Route;

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

Route::get('projects', [ \Sheets\YearToDate\Http\Controllers\ProjectController::class, 'index' ])->name('YearToDate::get-projects');
Route::get('projects/{project}', [ \Sheets\YearToDate\Http\Controllers\ProjectController::class, 'show' ])->name('YearToDate::show-project');
