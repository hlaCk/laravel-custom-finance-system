<?php

namespace Database\Factories\Info\Contractor;

use App\Models\Info\Contractor\Contractor;
use App\Models\Info\Contractor\ContractorSpeciality;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Info\Contractor\Contractor>
 */
class ContractorFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string|\App\Models\Info\Contractor\Contractor
     */
    protected $model = Contractor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'                     => $this->faker->firstNameMale(),
            'contractor_speciality_id' => ContractorSpeciality::inRandomOrder()->first()
                ?: ContractorSpeciality::factory(),
            'status'                   => $this->model::getDefaultStatus(),
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

    public function makeContractorSpeciality()
    {
        return $this->state(fn(array $attributes) => [ 'contractor_speciality_id' => ContractorSpeciality::factory() ]);
    }

    public function anyContractorSpeciality()
    {
        return $this->state(fn(array $attributes) => [ 'contractor_speciality_id' => ContractorSpeciality::inRandomOrder()->first() ]);
    }

}
