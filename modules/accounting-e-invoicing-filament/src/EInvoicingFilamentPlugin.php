<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\EInvoicingFilament\Resources\EInvoiceResource;

final class EInvoicingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-e-invoicing-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([EInvoiceResource::class]);
    }

    public function boot(Panel $panel): void {}
}
