<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\InventoryAccounting\Enums\InventoryStatus;
use Liberu\Accounting\InventoryAccounting\Queries\InventoryQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class InventoryAccounting extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view inventory.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-inventory-accounting-livewire::inventory', ['items' => app(InventoryQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? InventoryStatus::from($this->status) : null)]);
    }
}
