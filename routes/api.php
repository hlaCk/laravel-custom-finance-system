<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MediaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('rename-file',[ MediaController::class, 'renameFile']);
Route::delete('media-delete/{media}',[ MediaController::class, 'deleteFile']);

Route::get('expenses',[ \App\Http\Controllers\Api\Sheet\ExpenseController::class, 'all'])->name('expenses_all');
