<?php

use App\Filament\ModulePlugins;

it('discovers the three-way matching Filament plugin on the app panel', function (): void {
    expect(collect(app(ModulePlugins::class)->forPanel('app'))->map(fn ($plugin) => $plugin->getId())->all())
        ->toContain('accounting-three-way-matching');
});
