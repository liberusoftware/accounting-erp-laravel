<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Reimbursements\Models\ReimbursementLiability;
use Livewire\Component;
use Livewire\WithPagination;

final class Reimbursements extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view reimbursements.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-reimbursements::reimbursements', ['liabilities' => ReimbursementLiability::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->paginate(15)]);
    }
}
