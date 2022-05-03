<?php

namespace Database\Factories\Info\Project;

use App\Models\Info\Client;
use App\Models\Info\Project\Project;
use App\Models\Info\Project\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Info\Project\Project>
 */
class ProjectFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string|\App\Models\Info\Project\Project
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->streetName(),
            'cost' => $this->faker->numberBetween(20000,500000),
            'project_status_id' => ProjectStatus::first() ?: ProjectStatus::factory(),
            'client_id' => Client::onlyActive()->inRandomOrder()->first() ?: Client::factory(),
            'status' => $this->model::getDefaultStatus(),
        ];
    }

    /**
     * Indicate that the user is suspended.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->model::ACTIVE,
            ];
        });
    }

    /**
     * Indicate that the user is suspended.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->model::INACTIVE,
            ];
        });
    }

    public function makeClient()
    {
        return $this->state(fn(array $attributes) => [ 'client_id' => Client::factory() ]);
    }

    public function anyClient()
    {
        return $this->state(fn(array $attributes) => [ 'client_id' => Client::onlyActive()->inRandomOrder()->first() ]);
    }
}
