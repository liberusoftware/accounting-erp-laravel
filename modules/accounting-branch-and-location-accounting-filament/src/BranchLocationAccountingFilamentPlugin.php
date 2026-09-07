<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccountingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\BranchLocationAccountingFilament\Resources\BranchResource;

final class BranchLocationAccountingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-branch-and-location-accounting-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BranchResource::class]);
    }

    public function boot(Panel $panel): void {}
}
