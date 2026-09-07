<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigrationLivewire;

use Illuminate\Support\ServiceProvider;

final class SpreadsheetMigrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-spreadsheet-migration');
    }
}
