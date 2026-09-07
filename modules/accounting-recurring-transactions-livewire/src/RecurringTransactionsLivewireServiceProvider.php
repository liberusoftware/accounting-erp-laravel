<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\RecurringTransactionsLivewire\Livewire\RecurringTemplates;
use Livewire\Livewire;

final class RecurringTransactionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-recurring-templates', RecurringTemplates::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-recurring-transactions');
    }
}
