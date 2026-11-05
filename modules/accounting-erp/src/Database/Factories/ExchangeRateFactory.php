<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\Currency;
use Liberu\Accounting\Erp\Models\ExchangeRate;

/**
 * @extends Factory<ExchangeRate>
 */
class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_currency_id' => Currency::factory(),
            'to_currency_id' => Currency::factory(),
            'rate' => $this->faker->randomFloat(6, 0.5, 2.0),
            'date' => now()->toDateString(),
        ];
    }
}
