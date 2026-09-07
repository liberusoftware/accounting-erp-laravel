<?php

namespace Liberu\Accounting\PoliciesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PoliciesFilament\Resources\PolicyRuleResource;

final class AccountingPoliciesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-policies';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PolicyRuleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
