<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;
use Liberu\Accounting\SalesInvoicing\Events\InvoiceFinalized;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class FinalizeInvoice
{
    public function handle(SalesInvoice $invoice, ?string $actor = null): SalesInvoice
    {
        return DB::transaction(function () use ($invoice, $actor) {
            $invoice = SalesInvoice::query()->lockForUpdate()->findOrFail($invoice->id);
            if ($invoice->status !== InvoiceStatus::Approved) {
                throw new InvalidInvoice('Only approved invoices may be finalized.');
            }$invoice->update(['status' => InvoiceStatus::Final]);
            DB::afterCommit(fn () => event(new InvoiceFinalized($invoice->fresh('lines'), $actor)));

            return $invoice->refresh()->load('lines');
        });
    }
}
