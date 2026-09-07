<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Queries\ReceiptQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Receipts extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view receipts.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('module-accounting-goods-and-service-receipts-livewire::receipts', ['receipts' => app(ReceiptQuery::class)->paginate(auth()->user()?->current_team_id, $this->status !== '' ? ReceiptStatus::from($this->status) : null)]);
    }
}
