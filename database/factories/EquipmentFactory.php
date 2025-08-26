<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'model' => $this->faker->bothify('??-####'),
            'watt' => $this->faker->randomFloat(0, 1000, 100000),
        ];
    }
}