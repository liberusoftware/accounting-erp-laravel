<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagementFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\ReceiptManagementFilament\Resources\ReceiptResource;

final class ReceiptManagementFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-receipt-management-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReceiptResource::class]);
    }

    public function boot(Panel $panel): void {}
}
