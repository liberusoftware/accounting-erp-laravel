<?php

namespace Liberu\Accounting\ErpFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class ErpFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-erp';
    }

    public function register(Panel $panel): void
    {
        $sections = match ($panel->getId()) {
            'admin' => ['Admin', 'App'],
            'app' => ['App'],
            'customer' => ['Customer'],
            'vendor' => ['Vendor'],
            default => [],
        };

        foreach ($sections as $section) {
            $namespace = __NAMESPACE__.'\\Filament\\'.$section;
            $directory = __DIR__.'/Filament/'.$section;
            $panel->discoverResources(in: $directory.'/Resources', for: $namespace.'\\Resources')
                ->discoverPages(in: $directory.'/Pages', for: $namespace.'\\Pages')
                ->discoverWidgets(in: $directory.'/Widgets', for: $namespace.'\\Widgets');
        }

        if ($panel->getId() === 'admin') {
            $panel->discoverWidgets(in: __DIR__.'/Filament/Widgets', for: __NAMESPACE__.'\\Filament\\Widgets');
        }
    }

    public function boot(Panel $panel): void {}
}
