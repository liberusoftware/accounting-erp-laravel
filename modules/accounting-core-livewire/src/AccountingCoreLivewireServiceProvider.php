<?php

namespace Liberu\Accounting\CoreLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CoreLivewire\Livewire\AccountingSettings;
use Liberu\Accounting\CoreLivewire\Livewire\Books;
use Liberu\Accounting\CoreLivewire\Livewire\LegalEntities;
use Livewire\Livewire;

final class AccountingCoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-core-livewire');
        Livewire::component('module-accounting-core-legal-entities', LegalEntities::class);
        Livewire::component('module-accounting-core::legal-entities', LegalEntities::class);
        Livewire::component('module-accounting-core-books', Books::class);
        Livewire::component('module-accounting-core::books', Books::class);
        Livewire::component('module-accounting-core-settings', AccountingSettings::class);
        Livewire::component('module-accounting-core::accounting-settings', AccountingSettings::class);
    }
}
