<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\AccountsPayable\Queries\ControlAccountReconciliationQuery;
use Livewire\Component;

final class Reconciliation extends Component
{
    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view payables.');
        }
    }

    public function render(): mixed
    {
        return view('accounting-accounts-payable::reconciliation', ['reconciliation' => app(ControlAccountReconciliationQuery::class)->handle()]);
    }
}
