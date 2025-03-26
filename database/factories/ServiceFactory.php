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
            'costo' => $this->faker->randomFloat(2, 10, 500),
            'fecha_inicio' => $this->faker->date(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo', 'pendiente']),
        ];
    }
}
