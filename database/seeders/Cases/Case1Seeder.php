<?php

namespace Database\Seeders\Cases;

use App\Models\Info\Project\Project;
use App\Models\Sheet\Credit;
use App\Models\Sheet\Expense;
use Illuminate\Database\Seeder;

class Case1Seeder extends Seeder
{
    public int $projects = 20;
    public int $credits = 100;
    public int $expenses = 100;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $getCreditsLimit = fn() => random_int(1, $this->credits);
        $getExpensesLimit = fn() => random_int(1, $this->expenses);

        setCurrentLocale('ar');
        Project::factory()
               ->count($this->projects)
               ->makeClient()
               ->create()
               ->each(function (Project $project) use ($getCreditsLimit,$getExpensesLimit){
                   Credit::factory()
                         ->count($getCreditsLimit())
                         ->forProject($project)
                         ->create();

                   Expense::factory()
                          ->count($getExpensesLimit())
                          ->forProject($project)
                          ->create();
               });

    }
}
