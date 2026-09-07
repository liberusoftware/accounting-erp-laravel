<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability as ProfitabilityRecord;
use Livewire\Component;
use Livewire\WithPagination;

final class ProjectProfitability extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view project profitability.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-project-profitability-livewire::project-profitability', ['records' => ProfitabilityRecord::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('period_start')->paginate(15)]);
    }
}
