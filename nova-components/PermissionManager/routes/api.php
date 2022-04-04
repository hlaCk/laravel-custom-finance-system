<?php

use App\Models\Settings\Permission;
use App\Models\Settings\Role;
use Illuminate\Http\Request;
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

// Route::get('/endpoint', function (Request $request) {
//     //
// });

Route::get('roles/{id}/permissions', function (Request $request, $id) {
    return ([
        'role' => Role::with('permissions')->find($id),
        'permissions' => Permission::get()->groupBy('group'),
    ]);
});

Route::post('roles/{id}/permissions', function (Request $request, $id) {
    $role = Role::with('permissions')->find($id);
    $role->syncPermissions(Permission::whereIn('id', $request->permissions)->get());
    \Artisan::call('permission:cache-reset');
    return [
        'success' => true,
    ];
});
