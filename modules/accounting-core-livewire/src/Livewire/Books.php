<?php

namespace Liberu\Accounting\CoreLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\Core\Actions\SaveBook;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Livewire\Component;

final class Books extends Component
{
    public int|string $legalEntityId = '';

    public string $name = '';

    public string $code = '';

    public string $accountingBasis = 'accrual';

    public function save(SaveBook $save): void
    {
        $data = $this->validate(['legalEntityId' => ['required', 'exists:accounting_legal_entities,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50'], 'accountingBasis' => ['required', 'in:accrual,cash']]);
        $save->handle(null, ['legal_entity_id' => $data['legalEntityId'], 'name' => $data['name'], 'code' => $data['code'], 'accounting_basis' => $data['accountingBasis'], 'is_active' => true]);
        $this->reset('name', 'code');
        $this->dispatch('book-created');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-core-livewire::livewire.books', ['books' => Book::query()->with('legalEntity')->latest()->paginate(15), 'legalEntities' => LegalEntity::query()->orderBy('name')->get()]);
    }
}
