<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstLivewire\Livewire;

use Liberu\Accounting\SalesTaxAndGst\Queries\SalesTaxRecordQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class SalesTaxRecords extends Component
{
    use WithPagination;

    public string $status = '';

    public function render(): mixed
    {
        abort_unless(auth()->check(), 403);

        return view('module-accounting-sales-tax-and-gst::records', ['records' => app(SalesTaxRecordQuery::class)->paginate(null, null, $this->status ?: null)]);
    }
}
