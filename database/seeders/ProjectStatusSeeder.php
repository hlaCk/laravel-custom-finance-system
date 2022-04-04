<?php

namespace Database\Seeders;

use App\Models\Info\Project\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
                    'New',
                    'Under Construction',
                    'Finished',
                    'Canceled',
                ])->map(function ($name) {
            return ProjectStatus::firstOrCreate(compact('name'));
        });
    }
}
