<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagementLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\ReceiptManagement\Models\Receipt;
use Livewire\Component;
use Livewire\WithPagination;

final class Receipts extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view receipts.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-receipt-management::receipts', ['receipts' => Receipt::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->paginate(15)]);
    }
}
