<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCostingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\ProjectCosting\Models\ProjectCost;
use Livewire\Component;
use Livewire\WithPagination;

final class ProjectCosts extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view project costs.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-project-costing::project-costs', ['costs' => ProjectCost::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('occurred_on')->paginate(15)]);
    }
}
