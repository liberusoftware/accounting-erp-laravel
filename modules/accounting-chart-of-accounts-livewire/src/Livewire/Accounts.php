<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\ChartOfAccounts\Actions\SaveAccount;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Livewire\Component;

final class Accounts extends Component
{
    public string $legalEntityId = '';

    public string $code = '';

    public string $name = '';

    public string $type = 'asset';

    public string $normalBalance = '';

    public string $parentId = '';

    public function save(SaveAccount $save): void
    {
        $validated = $this->validate([
            'legalEntityId' => ['required', 'integer', 'exists:accounting_legal_entities,id'],
            'code' => ['required', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'normalBalance' => ['nullable', 'in:debit,credit'],
            'parentId' => ['nullable', 'integer', 'exists:accounting_chart_accounts,id'],
        ]);

        $save->handle([
            'legal_entity_id' => $validated['legalEntityId'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'normal_balance' => $validated['normalBalance'] ?: null,
            'parent_id' => $validated['parentId'] ?: null,
        ]);

        $this->reset('code', 'name', 'normalBalance', 'parentId');
        $this->dispatch('account-created');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-chart-of-accounts-livewire::livewire.accounts', [
            'accounts' => Account::query()->where('legal_entity_id', $this->legalEntityId ?: 0)->with('parent')->latest()->paginate(25),
        ]);
    }
}
