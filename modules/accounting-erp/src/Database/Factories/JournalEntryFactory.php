<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\User;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'entry_number' => $this->faker->unique()->numerify('JE-#####'),
            'entry_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'reference_number' => $this->faker->optional()->numerify('REF-####'),
            'memo' => $this->faker->optional()->sentence(),
            'entry_type' => $this->faker->randomElement(['general', 'adjusting', 'closing', 'reversing']),
            'is_approved' => false,
            'is_posted' => false,
        ];
    }

    public function posted(): static
    {
        return $this->state(['is_posted' => true, 'posted_at' => now()]);
    }
}
