<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'cost' => $this->faker->randomFloat(2, 10, 500),
            'ini_date' => $this->faker->date(),
            'state' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}
