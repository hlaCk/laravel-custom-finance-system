<?php

namespace Database\Factories\Info\Project;

use App\Models\Info\Project\Project;
use App\Models\Info\Project\ProjectCost;

class ProjectCostFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectCost::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => $this->faker->word(),
            'cost'       => $this->faker->numberBetween(5000, 100000),
            'project_id' => Project::onlyActive()->inRandomOrder()->first() ?: Project::factory(),
        ];
    }

    public function makeProject(...$parameters)
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => Project::factory(...$parameters) ]);
    }

    public function anyProject()
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => Project::onlyActive()->inRandomOrder()->first() ]
        );
    }

    /**
     * @param int|\App\Models\Abstracts\Model $project
     *
     * @return static
     */
    public function forProject($project)
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => $project ]);
    }
}
