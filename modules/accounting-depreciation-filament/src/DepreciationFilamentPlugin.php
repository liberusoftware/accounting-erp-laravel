<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepreciationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\DepreciationFilament\Resources\DepreciationScheduleResource;

final class DepreciationFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-depreciation';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([DepreciationScheduleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
