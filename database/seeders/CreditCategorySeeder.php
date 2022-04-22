<?php

namespace Database\Seeders;

use App\Models\Info\CreditCategory;
use Illuminate\Database\Seeder;

class CreditCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
                    "Cash",
                ])->map(function ($name) {
            return CreditCategory::firstOrCreate(compact('name'));
        });
    }
}
