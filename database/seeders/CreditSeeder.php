<?php

namespace Database\Seeders;

use App\Models\Sheet\Credit;
use Illuminate\Database\Seeder;

class CreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Credit::factory()
              ->count(100)
              ->makeProject()
              ->create();
    }
}
