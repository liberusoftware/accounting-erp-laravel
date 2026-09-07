<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingLivewire\Livewire;

use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;
use Livewire\Component;

final class Invoices extends Component
{
    public function render(): mixed
    {
        return view('accounting-sales-invoicing-livewire::invoices', ['invoices' => SalesInvoice::latest()->paginate(25)]);
    }
}
