<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PayrollJournalsFilament\Resources\PayrollJournalResource;

final class PayrollJournalsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-payroll-journals';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PayrollJournalResource::class]);
    }

    public function boot(Panel $panel): void {}
}
