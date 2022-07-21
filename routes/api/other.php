<?php

use App\Http\Controllers\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('rename-file', [ MediaController::class, 'renameFile' ]);
Route::delete('media-delete/{media}', [ MediaController::class, 'deleteFile' ]);


//Route::get('expenses',[ \App\Http\Controllers\Api\Sheet\ExpenseController::class, 'all'])->name('expenses_all');
