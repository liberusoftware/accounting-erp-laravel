<?php

namespace Liberu\Accounting\PoliciesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PoliciesLivewire\Livewire\PolicyRules;
use Livewire\Livewire;

final class AccountingPoliciesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-policies-livewire');
        Livewire::component('module-accounting-policies-policy-rules', PolicyRules::class);
        Livewire::component('module-accounting-policies::policy-rules', PolicyRules::class);
    }
}
