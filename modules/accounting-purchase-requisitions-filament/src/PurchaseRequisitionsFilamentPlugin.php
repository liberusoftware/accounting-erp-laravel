<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\PurchaseRequisitionsFilament\Resources\PurchaseRequisitionResource;

final class PurchaseRequisitionsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-accounting-purchase-requisitions';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PurchaseRequisitionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
