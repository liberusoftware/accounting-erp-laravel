<?php

declare(strict_types=1);
use Liberu\Accounting\CashCollectionAssistantFilament\CashCollectionAssistantFilamentPlugin;

it('exposes the stable Filament plugin identity', function (): void {
    expect(CashCollectionAssistantFilamentPlugin::make()->getId())->toBe('module-accounting-cash-collection-assistant-filament');
});
