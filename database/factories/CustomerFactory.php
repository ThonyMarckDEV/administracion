<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'codigo' => $this->faker->unique()->numerify('CUST-#####'),
            'email' => $this->faker->unique()->safeEmail(),
            'dni' => $this->faker->optional()->numerify('#########'),
            'ruc' => $this->faker->optional()->numerify('1##########'),
            'client_type_id' => $this->faker->numberBetween(1, 2),
            'state' => $this->faker->boolean(),
        ];
    }
}
