<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\Vat\Models\VatRecord;
use Livewire\Component;
use Livewire\WithPagination;

final class VatRecords extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view VAT records.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-vat::records', ['records' => VatRecord::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest('occurred_on')->paginate(15)]);
    }
}
