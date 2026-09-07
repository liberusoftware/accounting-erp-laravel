<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovalsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\JournalApprovalsLivewire\Livewire\JournalApprovals;
use Livewire\Livewire;

final class JournalApprovalsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-journal-approvals::approvals', JournalApprovals::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-journal-approvals-livewire');
    }
}
