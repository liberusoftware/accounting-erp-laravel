<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Services\PostingReversalService;
use Throwable;

class UnpostInvoice extends Command
{
    #[\Override]
    protected $signature = 'invoices:unpost {invoice : Invoice ID}';

    #[\Override]
    protected $description = 'Unpost (reverse) an invoice\'s general-ledger entry';

    public function handle(PostingReversalService $service): int
    {
        $invoice = Invoice::find($this->argument('invoice'));
        if (! $invoice instanceof Invoice) {
            $this->error("Invoice {$this->argument('invoice')} not found.");

            return self::FAILURE;
        }

        if ($invoice->journal_entry_id === null) {
            $this->info("Invoice {$invoice->id} is not posted; skipped.");

            return self::SUCCESS;
        }

        try {
            $service->reverseInvoice($invoice);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Unposted invoice {$invoice->id}.");

        return self::SUCCESS;
    }
}
