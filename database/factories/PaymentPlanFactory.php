<?php

namespace Database\Factories;

use App\Models\Period;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Testing\Fakes\Fake;

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
        $paymentType = $this->faker->boolean();
        return [
            'service_id' => $this->faker->randomElement(Service::pluck('id')),
            'period_id' => $this->faker->randomElement(Period::pluck('id')),
            'payment_type' => $paymentType,
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'duration' => $paymentType ? 1 : 3,
            'state' => true,
        ];
    }
}
