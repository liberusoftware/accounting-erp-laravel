<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\SalesInvoicingFilament\Resources\SalesInvoiceResource;

final class SalesInvoicingFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'accounting-sales-invoicing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SalesInvoiceResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return new self();
    }
}
