<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\RecurringTransactions\Models\RecurringTemplate;
use Livewire\Component;
use Livewire\WithPagination;

final class RecurringTemplates extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view recurring transactions.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-recurring-transactions::recurring-templates', ['templates' => RecurringTemplate::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->paginate(15)]);
    }
}
