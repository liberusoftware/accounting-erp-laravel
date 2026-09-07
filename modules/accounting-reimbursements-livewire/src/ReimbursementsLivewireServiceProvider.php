<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ReimbursementsLivewire\Livewire\Reimbursements;
use Livewire\Livewire;

final class ReimbursementsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-reimbursements', Reimbursements::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-reimbursements');
    }
}
