<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\AccountReconciliations\Queries\AccountReconciliationQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Reconciliations extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view account reconciliations.');
        }
    }

    public function render(): mixed
    {
        return view('accounting-account-reconciliations::reconciliations', ['reconciliations' => app(AccountReconciliationQuery::class)->paginate(auth()->user()?->current_team_id, $this->status ?: null)]);
    }
}
