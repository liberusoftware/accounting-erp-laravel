<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;
use Liberu\Accounting\EInvoicing\Queries\EInvoiceQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class Documents extends Component
{
    use WithPagination;

    public string $status = '';

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view e-invoices.');
        }
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        return view('accounting-e-invoicing-livewire::documents', ['documents' => app(EInvoiceQuery::class)->paginate(null, $this->status !== '' ? DocumentStatus::tryFrom($this->status) : null)]);
    }
}
