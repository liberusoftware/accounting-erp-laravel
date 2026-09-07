<?php

declare(strict_types=1);

namespace Liberu\Accounting\CopilotLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CopilotLivewire\Livewire\Requests;
use Livewire\Livewire;

final class CopilotLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-copilot-livewire');
        Livewire::component('accounting-copilot-requests', Requests::class);
    }
}
