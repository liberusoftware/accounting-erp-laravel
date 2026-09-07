<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class SetInvoiceBranding
{
    public function handle(SalesInvoice $invoice, array $branding): SalesInvoice
    {
        if ($invoice->status->value !== 'draft') {
            throw new InvalidInvoice('Branding can only be changed on a draft invoice.');
        }$invoice->update(['branding' => $branding]);

        return $invoice->refresh();
    }
}
