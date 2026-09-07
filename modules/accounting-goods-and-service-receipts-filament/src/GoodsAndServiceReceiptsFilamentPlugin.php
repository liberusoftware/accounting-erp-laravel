<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\GoodsAndServiceReceiptsFilament\Resources\ReceiptResource;

final class GoodsAndServiceReceiptsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-goods-and-service-receipts-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReceiptResource::class]);
    }

    public function boot(Panel $panel): void {}
}
