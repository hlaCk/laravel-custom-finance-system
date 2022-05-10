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
                    [ 'name' => "Rent / Mortgage", 'has_contractor' => false ],
                    [ 'name' => "Materials / Maintenance", 'has_contractor' => false ],
                    [ 'name' => "Grocery / Food Items", 'has_contractor' => false ],
                    [ 'name' => "Petrol / Transportation", 'has_contractor' => false ],
                    [ 'name' => "Utilities", 'has_contractor' => false ],
                    [ 'name' => "Cash to Imad", 'has_contractor' => true ],
                    [ 'name' => "Cash to / Employee / Site / Labour", 'has_contractor' => true ],
                    [ 'name' => "Stationary", 'has_contractor' => false ],
                    [ 'name' => "Government Charges and Fees", 'has_contractor' => false ],
                    [ 'name' => "Store", 'has_contractor' => false ],
                    [ 'name' => "Miscellaneous", 'has_contractor' => false ],
                    [ 'name' => "Salary", 'has_contractor' => true ],
                    [ 'name' => "Overtime", 'has_contractor' => true ],
                    [ 'name' => "Insurance", 'has_contractor' => false ],
                ])->map(function ($row) {
            return EntryCategory::firstOrCreate($row);
        });
    }
}
