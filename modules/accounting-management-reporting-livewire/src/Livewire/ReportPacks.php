<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\ManagementReporting\Queries\ReportQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class ReportPacks extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view management reports.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-management-reporting-livewire::report-packs', ['packs' => app(ReportQuery::class)->packs(auth()->user()?->current_team_id, $this->status ?: null)]);
    }
}
