<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankRulesLivewire\Livewire\Rules;
use Livewire\Livewire;

final class BankRulesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-bank-rules');
        Livewire::component('module-accounting-bank-rules::rules', Rules::class);
        Livewire::component('module-accounting-bank-rules-rules', Rules::class);
    }
}
