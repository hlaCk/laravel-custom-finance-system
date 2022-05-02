<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractFactory extends Factory
{
    public static $custom_fakers = [];

    public function make($attributes = [], ?Model $parent = null)
    {
        if( $this->faker->locale() !== currentFakerLocale() ) {
            $this->faker = \Faker\Factory::create(currentFakerLocale());
        }

        return parent::make($attributes, $parent);
    }

    public function makeFaker(?string $locale = null)
    {
        $faker = static::$custom_fakers[ $locale ] ?? null;
        if( is_null($faker) ) {
            $currentLocale = currentLocale();
            setCurrentLocale($locale);
            $faker = \Faker\Factory::create(currentFakerLocale());
            setCurrentLocale($currentLocale);
        }

        return static::$custom_fakers[ $locale ] = $faker;
    }
}
