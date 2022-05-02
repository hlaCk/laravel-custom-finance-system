<?php

namespace Database\Factories\Sheet;

use App\Models\Info\EntryCategory;
use App\Models\Info\ExpenseCategory;
use App\Models\Info\Project\Project;
use App\Models\Sheet\Expense;

class ExpenseFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string|\App\Models\Sheet\Expense
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date'               => $this->faker->dateTimeBetween(getDefaultFromDate(), getDefaultToDate()),
            'amount'             => $this->faker->numberBetween(10000, 100000),
            'vat_included'       => $this->faker->boolean(),
            'remarks'            => $this->faker->sentence((int)$this->faker->randomNumber(1)),
            'project_id'         => Project::onlyActive()->inRandomOrder()->first() ?: Project::factory(),
            'entry_category_id' => EntryCategory::onlyActive()->inRandomOrder()->first() ?: EntryCategory::factory(),
        ];
    }

    public function makeProject(...$parameters)
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => Project::factory(...$parameters) ]);
    }

    public function anyProject()
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => Project::onlyActive()->inRandomOrder()->first() ]);
    }

    /**
     * @param int|\App\Models\Abstracts\Model $project
     *
     * @return \Database\Factories\Sheet\CreditFactory
     */
    public function forProject($project)
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => $project ]);
    }
}
