<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ReimbursementsFilament\Resources\ReimbursementLiabilityResource;

final class ReimbursementsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-reimbursements-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReimbursementLiabilityResource::class]);
    }

    public function boot(Panel $panel): void {}
}
