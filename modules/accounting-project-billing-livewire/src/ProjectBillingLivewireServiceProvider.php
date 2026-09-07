<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBillingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ProjectBillingLivewire\Livewire\ProjectBillings;
use Livewire\Livewire;

final class ProjectBillingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-project-billings', ProjectBillings::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-project-billing');
    }
}
