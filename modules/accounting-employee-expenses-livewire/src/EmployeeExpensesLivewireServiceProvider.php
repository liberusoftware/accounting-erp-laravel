<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\EmployeeExpensesLivewire\Livewire\Claims;
use Livewire\Livewire;

final class EmployeeExpensesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-employee-expenses-livewire');
        Livewire::component('module-accounting-employee-expenses::claims', Claims::class);
        Livewire::component('employee-expenses', Claims::class);
    }
}
