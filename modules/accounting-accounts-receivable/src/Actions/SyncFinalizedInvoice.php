<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class SyncFinalizedInvoice
{
    public function handle(SalesInvoice $invoice): ReceivableOpenItem
    {
        return DB::transaction(function () use ($invoice) {
            $existing = ReceivableOpenItem::query()->where('source_type', SalesInvoice::class)->where('source_id', (string) $invoice->getKey())->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            return app(CreateOpenItem::class)->handle(['party_id' => $invoice->party_id, 'source_type' => SalesInvoice::class, 'source_id' => (string) $invoice->getKey(), 'reference' => $invoice->invoice_number, 'issued_on' => $invoice->invoice_date, 'due_on' => $invoice->due_on, 'original_amount' => $invoice->total, 'currency' => $invoice->currency, 'metadata' => ['invoice_number' => $invoice->invoice_number]]);
        });
    }
}
