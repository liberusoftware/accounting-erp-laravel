<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Liberu\Accounting\Erp\Models\ConnectedAccount;

/**
 * @extends Factory<ConnectedAccount>
 */
class ConnectedAccountFactory extends Factory
{
    #[\Override]
    protected $model = ConnectedAccount::class;

    public function definition(): array
    {
        return [
            'provider' => 'github',
            'provider_id' => $this->faker->numerify('########'),
            'token' => Str::random(432),
            'refresh_token' => Str::random(432),
        ];
    }
}
