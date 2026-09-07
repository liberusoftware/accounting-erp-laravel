<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Events;

use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class InvoiceCreated
{
    public function __construct(public readonly SalesInvoice $invoice) {}
}
