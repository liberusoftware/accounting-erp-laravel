<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Estimate;

/**
 * @extends Factory<Estimate>
 */
class EstimateFactory extends Factory
{
    protected $model = Estimate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'estimate_date' => now()->toDateString(),
            'expiration_date' => now()->addDays(30)->toDateString(),
            'total_amount' => $this->faker->randomFloat(2, 10, 5000),
        ];
    }
}
