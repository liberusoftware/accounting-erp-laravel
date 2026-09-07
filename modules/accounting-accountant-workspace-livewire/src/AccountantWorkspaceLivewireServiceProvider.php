<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountantWorkspaceLivewire\Livewire\Workspace;
use Livewire\Livewire;

final class AccountantWorkspaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-accountant-workspace');
        Livewire::component('accounting-accountant-workspace', Workspace::class);
    }
}
