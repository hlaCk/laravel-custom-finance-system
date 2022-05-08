<?php

namespace Database\Seeders\Cases;

use App\Models\Info\Contractor\Contractor;
use App\Models\Info\Project\Project;
use App\Models\Info\Project\ProjectCost;
use App\Models\Sheet\Credit;
use App\Models\Sheet\Expense;
use Database\Seeders\ContractorSeeder;
use Illuminate\Database\Seeder;

class Case1Seeder extends Seeder
{
    public int $projects = 10;
    public int $project_costs = 3;
    public int $contractors = 6;
    public int $credits = 10;
    public int $expenses = 10;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->callWith(ContractorSeeder::class, [
            'limit' => $this->contractors,
        ]);

        $getRandomLimit =
            fn($attr) => random_int(
                is_array($attr) ? $attr[ 0 ] : 1,
                is_array($attr) ? $attr[ 1 ] : ($this->$attr ?? $attr)
            );

//        setCurrentLocale('ar');
        Project::factory()
               ->count($this->projects)
//               ->hasContractors($getRandomLimit('contractors'))
               ->makeClient()
               ->create()
               ->each(function (Project $project) use ($getRandomLimit) {
                   if( $getRandomLimit([ 0, 1 ]) ) {
                       ProjectCost::factory()
                                  ->count($getRandomLimit('project_costs'))
                                  ->forProject($project)
                                  ->create();
                   }

                   if( $contractors_count = $getRandomLimit([ 0, $this->contractors ]) ) {
                       $contractors = Contractor::inRandomOrder()
                                                ->limit($contractors_count)
                                                ->pluck('name', 'id')
                                                ->mapWithKeys(function ($name, $id) use ($project) {
                                                    return [
                                                        $id => [
                                                            'date'     => $project->created_at,
                                                            'remarks'  => "Contractor {$id}:{$name} Project {$project->id}:{$project->name}",
                                                            'unit'     => 'meter',
                                                            'quantity' => 1,
                                                            'price'    => 2,
//                                                            'total'    => 2,
                                                            //                                                            'deleted_at'=>null,
                                                        ],
                                                    ];
                                                });

                       $project->contractors()->sync($contractors);
                   }

                   Credit::factory()
                         ->count($getRandomLimit('credits'))
                         ->forProject($project)
                         ->create();

                   Expense::factory()
                          ->count($getRandomLimit('expenses'))
                          ->forProject($project)
                          ->create();
               });

    }
}
