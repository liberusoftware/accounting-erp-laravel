<?php

use App\Filament\ModulePlugins;

it('discovers the sales invoicing Filament plugin on the app panel', function (): void {
    expect(collect(app(ModulePlugins::class)->forPanel('app'))->map(fn ($plugin) => $plugin->getId())->all())
        ->toContain('accounting-sales-invoicing');
});
