<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\MigrationFrameworkFilament\Resources\MigrationBatchResource;

final class MigrationFrameworkFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-migration-framework-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([MigrationBatchResource::class]);
    }

    public function boot(Panel $panel): void {}
}
