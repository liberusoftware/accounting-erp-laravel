<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class RecordDeposit
{
    public function handle(SalesInvoice $invoice, array $attributes): SalesInvoice
    {
        return DB::transaction(function () use ($invoice, $attributes) {
            $invoice = SalesInvoice::query()->lockForUpdate()->findOrFail($invoice->id);
            $amount = (float) ($attributes['amount'] ?? 0);
            if ($invoice->status !== InvoiceStatus::Final || $amount <= 0 || $amount > $invoice->outstanding()) {
                throw new InvalidInvoice('Deposits require a final invoice and cannot exceed the outstanding total.');
            }$invoice->deposits()->create($attributes + ['currency' => $invoice->currency]);

            return $invoice->refresh()->load('deposits');
        });
    }
}
