<?php

namespace Database\Seeders;

use App\Models\Sheet\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Expense::factory()
              ->count(100)
              ->makeProject()
              ->create();
    }
}
