<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Actions;

use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;
use Liberu\Accounting\SalesInvoicing\Exceptions\InvalidInvoice;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class GenerateInvoicePdf
{
    /** @return array{invoice_id:int,format:string,filename:string,immutable:bool} */
    public function handle(SalesInvoice $invoice, string $format = 'pdf'): array
    {
        if (! in_array($invoice->status, [InvoiceStatus::Approved, InvoiceStatus::Final], true)) {
            throw new InvalidInvoice('Only approved or final invoices may generate a PDF.');
        }if ($format !== 'pdf') {
            throw new InvalidInvoice('Only PDF output is supported by this boundary.');
        }

        return ['invoice_id' => $invoice->id, 'format' => 'pdf', 'filename' => 'invoice-'.$invoice->invoice_number.'.pdf', 'immutable' => $invoice->status === InvoiceStatus::Final];
    }
}
