<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;
use Livewire\Component;
use Livewire\WithPagination;

final class WorkforceCosts extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view workforce costs.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-workforce-costing::list', ['costs' => WorkforceCost::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('cost_date')->paginate(15)]);
    }
}
