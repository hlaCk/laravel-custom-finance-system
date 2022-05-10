<?php

namespace Database\Factories\Info;

use App\Models\Info\EntryCategory;

class EntryCategoryFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string|\App\Models\Info\EntryCategory
     */
    protected $model = EntryCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'           => $this->faker->firstName,
            'has_contractor' => $this->faker->boolean(),
            'status'         => $this->model::getDefaultStatus(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory|static
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
     * @return \Illuminate\Database\Eloquent\Factories\Factory|static
     */
    public function inActive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->model::INACTIVE,
            ];
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory|static
     */
    public function hasContractor()
    {
        return $this->state(function (array $attributes) {
            return [
                'has_contractor' => true,
            ];
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory|static
     */
    public function doesntHaveContractor()
    {
        return $this->state(function (array $attributes) {
            return [
                'has_contractor' => false,
            ];
        });
    }
}
