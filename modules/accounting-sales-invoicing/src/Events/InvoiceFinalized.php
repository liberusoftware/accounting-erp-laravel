<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Events;

use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class InvoiceFinalized
{
    public function __construct(public readonly SalesInvoice $invoice, public readonly ?string $actor = null) {}
}
