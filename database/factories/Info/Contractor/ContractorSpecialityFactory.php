<?php

namespace Database\Factories\Info\Contractor;

use App\Models\Info\Contractor\ContractorSpeciality;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractorSpecialityFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContractorSpeciality::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->colorName(),
        ];
    }
}
