<?php

namespace Database\Seeders;

use App\Models\Info\Contractor\Contractor;
use Illuminate\Database\Seeder;

class ContractorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($limit = 5)
    {
        Contractor::factory()
                  ->count($limit)
                  ->anyContractorSpeciality()
                  ->create();
    }
}
