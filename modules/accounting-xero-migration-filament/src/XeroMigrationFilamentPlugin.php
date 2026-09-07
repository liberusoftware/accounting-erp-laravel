<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigrationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\XeroMigrationFilament\Resources\XeroConnectionResource;

final class XeroMigrationFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-xero-migration-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([XeroConnectionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
