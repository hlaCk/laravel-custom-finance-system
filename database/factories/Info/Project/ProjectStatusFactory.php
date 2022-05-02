<?php

namespace Database\Factories\Info\Project;

use App\Models\Info\Project\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectStatusFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->firstName,
        ];
    }
}
