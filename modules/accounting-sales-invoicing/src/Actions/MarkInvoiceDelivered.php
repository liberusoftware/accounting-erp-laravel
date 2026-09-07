<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class MarkInvoiceDelivered
{
    public function handle(SalesInvoice $invoice): SalesInvoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice = SalesInvoice::query()->lockForUpdate()->findOrFail($invoice->id);
            if ($invoice->status !== InvoiceStatus::Final) {
                throw new InvalidInvoice('Only final invoices may be delivered.');
            }$invoice->delivery_status = 'delivered';
            $invoice->delivered_at = now();
            $invoice->saveQuietly();

            return $invoice->refresh();
        });
    }
}
