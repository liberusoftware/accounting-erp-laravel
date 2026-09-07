<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\AccountsReceivable\Queries\CustomerSubledgerQuery;
use Livewire\Component;

final class Receivables extends Component
{
    public int $partyId;

    public function mount(int $partyId): void
    {
        $this->partyId = $partyId;
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view receivables.');
        }
    }

    public function render()
    {
        return view('accounting-accounts-receivable::receivables', ['ledger' => app(CustomerSubledgerQuery::class)->handle($this->partyId)]);
    }
}
