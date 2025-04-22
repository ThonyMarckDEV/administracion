<?php

namespace Database\Factories;

use App\Models\Period;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentPlan>
 */
class PaymentPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'period_id' => Period::factory(),
            'payment_type' => $this->faker->boolean(),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'duration' => $this->faker->numberBetween(1, 24),
            'state' => $this->faker->boolean(),
        ];
    }
}
