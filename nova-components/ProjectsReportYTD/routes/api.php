<?php

use Illuminate\Support\Facades\Route;
use Sheets\ProjectsReportYTD\Http\Controllers\ProjectController;

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
         ->name('ProjectsReportYTD::get-projects');

    Route::prefix('{project}')->group(function () {
        Route::get('', [ ProjectController::class, 'show' ])
             ->name('ProjectsReportYTD::show-project');

        // region: expenses
        Route::get('expenses', [ ProjectController::class, 'expenses_index' ])
             ->name('ProjectsReportYTD::get-project-expenses');

        Route::get('expenses_ytd_by_month', [ ProjectController::class, 'project_expenses_ytd_by_month_show' ])
             ->name('ProjectsReportYTD::get-project-expenses-ytd-by-month');

        Route::get('expenses_ytd_by_category', [ ProjectController::class, 'project_expenses_ytd_by_category_show' ])
             ->name('ProjectsReportYTD::get-project-expenses-ytd-by-category');

        Route::get(
            'expenses_ytd_by_category_month',
            [ ProjectController::class, 'project_expenses_ytd_by_category_month_show' ]
        )
             ->name('ProjectsReportYTD::get-project-expenses-ytd-by-category-month');
        // endregion: expenses


        // region: credits
        Route::get('credits', [ ProjectController::class, 'credits_index' ])
             ->name('ProjectsReportYTD::get-project-credits');

        Route::get('credits_ytd_by_month', [ ProjectController::class, 'project_credits_ytd_by_month_show' ])
             ->name('ProjectsReportYTD::get-project-credits-ytd-by-month');

        Route::get('credits_ytd_by_category', [ ProjectController::class, 'project_credits_ytd_by_category_show' ])
             ->name('ProjectsReportYTD::get-project-credits-ytd-by-category');

        Route::get(
            'credits_ytd_by_category_month',
            [ ProjectController::class, 'project_credits_ytd_by_category_month_show' ]
        )
             ->name('ProjectsReportYTD::get-project-credits-ytd-by-category-month');
        // endregion: credits
    });
});

Route::get(
    'entry_categories',
    [ ProjectController::class, 'entry_categories_index' ]
)->name('ProjectsReportYTD::get-entry-categories');
Route::get(
    'credit_categories',
    [ ProjectController::class, 'credit_categories_index' ]
)->name('ProjectsReportYTD::get-credit-categories');

Route::get('some/url', [ ProjectController::class, 'test' ])->name(
    'ProjectsReportYTD::test'
);
