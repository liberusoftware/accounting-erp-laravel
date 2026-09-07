<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccountingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BranchLocationAccountingLivewire\Livewire\Branches;
use Livewire\Livewire;

final class BranchLocationAccountingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-branch-location-livewire');
        Livewire::component('accounting-branches', Branches::class);
    }
}
