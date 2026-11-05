<?php

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\BankConnection;
use Liberu\Accounting\Erp\Models\User;

class BankConnectionFactory extends Factory
{
    #[\Override]
    protected $model = BankConnection::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bank_id' => $this->faker->uuid(),
            'institution_name' => $this->faker->randomElement([
                'Chase Bank',
                'Bank of America',
                'Wells Fargo',
                'Citibank',
                'US Bank',
                'Capital One',
            ]),
            'plaid_access_token' => 'access-sandbox-'.$this->faker->uuid(),
            'plaid_item_id' => 'item-'.$this->faker->uuid(),
            'plaid_institution_id' => 'ins_'.$this->faker->randomNumber(5),
            'plaid_cursor' => null,
            'status' => $this->faker->randomElement(['active', 'disconnected', 'error']),
            'last_synced_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'active',
        ]);
    }

    public function disconnected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'disconnected',
        ]);
    }

    public function withCursor(): static
    {
        return $this->state(fn (array $attributes): array => [
            'plaid_cursor' => 'cursor-'.$this->faker->uuid(),
        ]);
    }
}
