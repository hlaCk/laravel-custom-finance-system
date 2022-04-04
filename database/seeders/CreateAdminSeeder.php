<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = \App\Models\Info\User::firstOrCreate([
                                                     'name' => 'Admin',
                                                     'email' => 'admin@app.com',
                                                 ], [
                                                     'password' => 'Admin@123',
                                                     'locale' => 'en',
                                                 ]);
        ($role = Role::whereIn('name', [ 'admin' ])->first()) && $admin->assignRole($role);
    }
}
