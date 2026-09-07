<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\AccountsPayable\Queries\SupplierSubledgerQuery;
use Livewire\Component;

final class Payables extends Component
{
    public int $partyId;

    public ?string $error = null;

    public function mount(int $partyId): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view payables.');
        }
        if ($partyId < 1) {
            throw ValidationException::withMessages(['partyId' => 'A supplier is required.']);
        }
        $this->partyId = $partyId;
    }

    public function render(): mixed
    {
        return view('accounting-accounts-payable::payables', ['ledger' => app(SupplierSubledgerQuery::class)->handle($this->partyId)]);
    }
}
