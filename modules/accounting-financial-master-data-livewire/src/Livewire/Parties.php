<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\FinancialMasterData\Actions\SaveParty;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Livewire\Component;

final class Parties extends Component
{
    public string $legalEntityId = '';

    public string $type = 'customer';

    public string $name = '';

    public string $email = '';

    public string $reference = '';

    public function save(SaveParty $save): void
    {
        $data = $this->validate(['legalEntityId' => ['required', 'integer', 'exists:accounting_legal_entities,id'], 'type' => ['required', 'in:customer,supplier'], 'name' => ['required', 'string', 'max:255'], 'email' => ['nullable', 'email', 'max:255'], 'reference' => ['nullable', 'string', 'max:64']]);
        $save->handle(['legal_entity_id' => $data['legalEntityId'], 'type' => $data['type'], 'name' => $data['name'], 'email' => $data['email'] ?: null, 'reference' => $data['reference'] ?: null]);
        $this->reset('name', 'email', 'reference');
        $this->dispatch('party-created');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-financial-master-data-livewire::livewire.parties', ['parties' => Party::query()->where('legal_entity_id', $this->legalEntityId ?: 0)->latest()->paginate(25)]);
    }
}
