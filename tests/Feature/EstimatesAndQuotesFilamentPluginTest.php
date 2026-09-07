<?php

use App\Filament\ModulePlugins;

it('discovers the estimates and quotes Filament plugin on the app panel', function (): void {
    expect(collect(app(ModulePlugins::class)->forPanel('app'))->map(fn ($plugin) => $plugin->getId())->all())
        ->toContain('module-accounting-estimates-and-quotes-filament');
});
