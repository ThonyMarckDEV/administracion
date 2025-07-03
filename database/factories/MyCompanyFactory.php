<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MyCompany>
 */
class MyCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ruc' => $this->faker->unique()->numerify('20#########'), // RUC de 11 dígitos
            'razon_social' => $this->faker->company,
            'nombre_comercial' => $this->faker->companySuffix,
            'ubigueo' => $this->faker->numerify('15####'), // código ubigeo genérico
            'departamento' => 'Lima',
            'provincia' => 'Lima',
            'distrito' => 'Lima',
            'urbanizacion' => $this->faker->optional()->word,
            'direccion' => $this->faker->address,
            'cod_local' => $this->faker->numerify('0###'), // código local de 4 caracteres
        ];
    }
}
