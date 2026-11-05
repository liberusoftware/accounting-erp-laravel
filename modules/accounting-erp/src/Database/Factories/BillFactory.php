<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\Erp\Models\Vendor;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    protected $model = Bill::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'bill_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'total_amount' => $this->faker->randomFloat(2, 10, 5000),
        ];
    }
}
