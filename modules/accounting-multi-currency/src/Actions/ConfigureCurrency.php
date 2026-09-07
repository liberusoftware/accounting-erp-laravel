<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Actions;

use Liberu\Accounting\MultiCurrency\Enums\CurrencyRole;
use Liberu\Accounting\MultiCurrency\Exceptions\InvalidCurrency;
use Liberu\Accounting\MultiCurrency\Models\CurrencyProfile;

final class ConfigureCurrency
{
    public function handle(array $attributes): CurrencyProfile
    {
        $currency = strtoupper((string) ($attributes['currency'] ?? ''));
        $role = CurrencyRole::tryFrom((string) ($attributes['role'] ?? ''));
        if (! preg_match('/^[A-Z]{3}$/', $currency) || ! $role || blank($attributes['scope_ref'] ?? null)) {
            throw new InvalidCurrency('Scope, ISO currency, and supported role are required.');
        }

        return CurrencyProfile::updateOrCreate(['team_id' => $attributes['team_id'] ?? null, 'scope_ref' => $attributes['scope_ref'], 'role' => $role], ['currency' => $currency, 'is_active' => $attributes['is_active'] ?? true, 'metadata' => $attributes['metadata'] ?? null]);
    }
}
