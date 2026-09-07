<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AuditSupportLivewire\Livewire\AuditRequests;
use Livewire\Livewire;

final class AuditSupportLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-audit-support');
        Livewire::component('accounting-audit-support', AuditRequests::class);
    }
}
