<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class ApproveInvoice
{
    public function handle(SalesInvoice $invoice): SalesInvoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice = SalesInvoice::query()->lockForUpdate()->findOrFail($invoice->id);
            if ($invoice->status !== InvoiceStatus::Draft) {
                throw new InvalidInvoice('Only draft invoices may be approved.');
            }$invoice->update(['status' => InvoiceStatus::Approved]);

            return $invoice->refresh();
        });
    }
}
