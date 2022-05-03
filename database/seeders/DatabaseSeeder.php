<?php

namespace Database\Seeders;

use Database\Seeders\Cases\Case1Seeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(CreateAdminSeeder::class);
        $this->call(EntryCategorySeeder::class);
        $this->call(CreditCategorySeeder::class);
        $this->call(ProjectStatusSeeder::class);
        $this->call(ContractorSpecialitySeeder::class);

        // todo: its for testing, comment it
        $this->call(Case1Seeder::class);
    }
}
