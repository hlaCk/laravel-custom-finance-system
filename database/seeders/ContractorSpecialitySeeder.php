<?php

namespace Database\Seeders;

use App\Models\Info\Contractor\ContractorSpeciality;
use Illuminate\Database\Seeder;

class ContractorSpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
                    'Blacksmith',
                    'Carpenter',
                    'Paved',
                    'Painter',
                ])->map(function ($name) {
            return ContractorSpeciality::firstOrCreate(compact('name'));
        });
    }
}
