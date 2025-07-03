<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'payment_plan_id' => PaymentPlan::factory(),
            'discount_id' => Discount::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('d-m-Y'),
            'payment_method' => $this->faker->randomElement(['efectivo', 'transferencia']),
            'reference' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pendiente', 'pagado', 'vencido']),
        ];
    }
}
