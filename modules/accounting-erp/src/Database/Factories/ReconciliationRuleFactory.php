<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\ReconciliationRule;

/**
 * @extends Factory<ReconciliationRule>
 */
class ReconciliationRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'match_field' => 'description',
            'match_operator' => 'contains',
            'match_value' => $this->faker->word(),
            'match_value_secondary' => null,
            'action_account_id' => null,
            'priority' => 0,
            'is_active' => true,
        ];
    }
}
