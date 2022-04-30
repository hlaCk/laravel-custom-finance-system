<?php

use Illuminate\Support\Facades\Route;
use Sheets\CompanyReportYTD\Http\Controllers\ProjectController;

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
    Route::post('expenses_credits_ytd_by_month', [ ProjectController::class, 'project_expenses_credits_ytd_by_month_show' ])
         ->name('CompanyReportYTD::get-project-expenses-credits-ytd-by-month');

    Route::post('credits_ytd_by_project', [ ProjectController::class, 'project_credits_ytd_by_project_show' ])
         ->name('CompanyReportYTD::get-project-credits-ytd-by-project');

    Route::post('expenses_ytd_by_project', [ ProjectController::class, 'project_expenses_ytd_by_project_show' ])
         ->name('CompanyReportYTD::get-project-expenses-ytd-by-project');

    Route::post('expenses_ytd_by_category_month', [ ProjectController::class, 'project_expenses_ytd_by_category_month_show' ])
         ->name('CompanyReportYTD::get-project-expenses-ytd-by-category-month');


//    Route::prefix('{project}')->group(function () {
//        Route::get('', [ ProjectController::class, 'show' ])
//             ->name('CompanyReportYTD::show-project');
//    });
});
