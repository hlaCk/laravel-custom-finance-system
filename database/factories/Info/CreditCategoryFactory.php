<?php

namespace Database\Factories\Info;

use App\Models\Info\CreditCategory;

class CreditCategoryFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string|\App\Models\Info\CreditCategory
     */
    protected $model = CreditCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'   => $this->faker->firstName,
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
    public function inActive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->model::INACTIVE,
            ];
        });
    }
}
