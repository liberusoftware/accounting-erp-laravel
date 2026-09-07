<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\MatchingIntelligenceLivewire\Livewire\MatchingSuggestions;
use Livewire\Livewire;

final class MatchingIntelligenceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-matching-intelligence::suggestions', MatchingSuggestions::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-matching-intelligence-livewire');
    }
}
