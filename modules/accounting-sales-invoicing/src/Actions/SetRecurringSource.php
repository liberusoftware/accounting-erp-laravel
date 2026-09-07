<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class SetRecurringSource
{
    public function handle(SalesInvoice $invoice, array $source): SalesInvoice
    {
        if ($invoice->status->value !== 'draft') {
            throw new InvalidInvoice('Recurring source can only be changed on a draft invoice.');
        }foreach (['source_type', 'source_id'] as $key) {
            if (blank($source[$key] ?? null)) {
                throw new InvalidInvoice("Recurring source field [{$key}] is required.");
            }
        }$invoice->update(['recurring_source' => $source]);

        return $invoice->refresh();
    }
}
