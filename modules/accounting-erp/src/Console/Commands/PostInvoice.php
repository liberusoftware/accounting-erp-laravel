<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Services\InvoicePostingService;
use RuntimeException;

class PostInvoice extends Command
{
    #[\Override]
    protected $signature = 'invoices:post {invoice : Invoice ID}';

    #[\Override]
    protected $description = 'Post an invoice to the general ledger';

    public function handle(InvoicePostingService $service): int
    {
        $invoice = Invoice::find($this->argument('invoice'));
        if (! $invoice instanceof Invoice) {
            $this->error("Invoice {$this->argument('invoice')} not found.");

            return self::FAILURE;
        }

        if ($invoice->journal_entry_id !== null) {
            $this->info("Invoice {$invoice->id} already posted (entry {$invoice->journal_entry_id}); skipped.");

            return self::SUCCESS;
        }

        try {
            $entry = $service->post($invoice);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Posted invoice {$invoice->id} to ledger (entry {$entry->id}).");

        return self::SUCCESS;
    }
}
