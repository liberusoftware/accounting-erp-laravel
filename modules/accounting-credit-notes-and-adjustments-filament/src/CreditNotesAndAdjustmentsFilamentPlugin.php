<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustmentsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class CreditNotesAndAdjustmentsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-credit-notes-and-adjustments-filament';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
