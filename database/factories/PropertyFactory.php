<?php

namespace Database\Factories;

use App\Models\Consumer;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'consumer_id' => Consumer::factory(),
            'account_no' => $this->faker->unique()->numerify('PROP######'),
            'address' => $this->faker->secondaryAddress(),
        ];
    }
}
