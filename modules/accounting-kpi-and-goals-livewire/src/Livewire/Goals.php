<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\KpiAndGoals\Queries\KpiQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Goals extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view KPI goals.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-kpi-and-goals-livewire::goals', ['goals' => app(KpiQuery::class)->goals(auth()->user()?->current_team_id, $this->status ?: null)]);
    }
}
