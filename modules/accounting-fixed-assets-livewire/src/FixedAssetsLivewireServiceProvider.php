<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsLivewire;

use Illuminate\Support\ServiceProvider;

final class FixedAssetsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-fixed-assets-livewire');

        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('module-accounting-fixed-assets::assets', Livewire\Assets::class);
            \Livewire\Livewire::component('fixed-assets', Livewire\Assets::class);
        }
    }
}
