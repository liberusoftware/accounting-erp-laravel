<?php

declare(strict_types=1);

use Liberu\Accounting\CorporateCardsFilament\CorporateCardsFilamentPlugin;

it('exposes the Corporate Cards Filament plugin', function (): void {
    expect(CorporateCardsFilamentPlugin::make()->getId())->toBe('module-accounting-corporate-cards-filament');
});
