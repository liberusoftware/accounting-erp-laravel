<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\Transaction;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'transaction_description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, -1000, 1000),
            'type' => $this->faker->randomElement(['credit', 'debit']),
            'status' => 'posted',
            'reconciled' => false,
        ];
    }
}
