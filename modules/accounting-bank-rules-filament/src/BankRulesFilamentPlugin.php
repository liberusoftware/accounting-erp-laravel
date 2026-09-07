<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\BankRulesFilament\Resources\BankRuleResource;

final class BankRulesFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-bank-rules';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BankRuleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
