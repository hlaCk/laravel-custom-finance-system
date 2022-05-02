<?php

namespace Database\Seeders;

use App\Models\Info\Project\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::factory()
              ->count(100)
              ->makeClient()
              ->create();
    }
}
