<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\BankAccounts\Queries\BankAccountBalanceQuery;
use Livewire\Component;

final class Accounts extends Component
{
    public ?int $legalEntityId = null;

    public function mount(?int $legalEntityId = null): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view bank accounts.');
        }
        $this->legalEntityId = $legalEntityId;
    }

    protected function rules(): array
    {
        return ['legalEntityId' => ['nullable', 'integer', 'min:1']];
    }

    public function render(): mixed
    {
        $this->validate();

        return view('accounting-bank-accounts::accounts', ['summary' => app(BankAccountBalanceQuery::class)->handle($this->legalEntityId)]);
    }
}
