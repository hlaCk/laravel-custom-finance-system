<?php

namespace Database\Factories\Info;

use App\Models\Info\Client;

class ClientFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string|\App\Models\Info\Client
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $en = $this->makeFaker('en');
        $ar = $this->makeFaker('ar');

        return [
            'name'   => [
                'en' => $en->firstName . ' ' . $en->lastName,
                'ar' => $ar->firstName . ' ' . $ar->lastName,
            ],
            'type'   => $this->model::DEFAULT_TYPE,
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

    public function cash()
    {
        return $this->state(fn(array $attributes) => [ 'type' => 'cash', ]);
    }

    public function credit()
    {
        return $this->state(fn(array $attributes) => [ 'type' => 'credit', ]);
    }
}
