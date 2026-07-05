<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'auditable_type' => User::class,
            'auditable_id' => User::factory(),
            'user_id' => User::factory(),
            'event' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'old_values' => null,
            'new_values' => ['name' => $this->faker->word()],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => 'PHPUnit',
        ];
    }
}
