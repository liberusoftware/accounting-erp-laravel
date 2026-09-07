<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Queries\ExpenseClaimQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Claims extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view employee expenses.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('accounting-employee-expenses-livewire::claims', ['claims' => app(ExpenseClaimQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? ClaimStatus::tryFrom($this->status) : null)]);
    }
}
