<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Listeners;

use Liberu\Accounting\AccountsReceivable\Actions\SyncFinalizedInvoice;
use Liberu\Accounting\SalesInvoicing\Events\InvoiceFinalized;

final class SyncFinalizedInvoiceListener
{
    public function handle(InvoiceFinalized $event): void
    {
        app(SyncFinalizedInvoice::class)->handle($event->invoice);
    }
}
