<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigrationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class SpreadsheetMigrationFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-spreadsheet-migration';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([Resources\MigrationTemplateResource::class]);
    }

    public function boot(Panel $panel): void {}
}
