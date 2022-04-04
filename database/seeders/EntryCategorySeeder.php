<?php

namespace Database\Seeders;

use App\Models\Info\EntryCategory;
use Illuminate\Database\Seeder;

class EntryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
                    "Rent / Mortgage",
                    "Materials / Maintenance",
                    "Grocery / Food Items",
                    "Petrol / Transportation",
                    "Utilities",
                    "Cash to Imad",
                    "Cash to / Employee / Site / Labour",
                    "Stationary",
                    "Government Charges and Fees",
                    "Store",
                    "Miscellaneous",
                    "Salary",
                    "Overtime",
                    "Insurance",
                ])->map(function ($name) {
            return EntryCategory::firstOrCreate(compact('name'));
        });
    }
}
