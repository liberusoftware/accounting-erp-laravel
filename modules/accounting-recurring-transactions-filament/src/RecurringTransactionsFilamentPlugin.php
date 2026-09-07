<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\RecurringTransactionsFilament\Resources\RecurringTemplateResource;

final class RecurringTransactionsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-recurring-transactions-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([RecurringTemplateResource::class]);
    }

    public function boot(Panel $panel): void {}
}
