<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrdersFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class SalesOrdersFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-sales-orders';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([Resources\SalesOrderResource::class]);
    }

    public function boot(Panel $panel): void {}
}
