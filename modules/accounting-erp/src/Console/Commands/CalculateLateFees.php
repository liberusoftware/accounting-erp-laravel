<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Models\Invoice;

class CalculateLateFees extends Command
{
    #[\Override]
    protected $signature = 'invoices:calculate-late-fees';

    #[\Override]
    protected $description = 'Calculate late fees for overdue invoices';

    public function handle(): void
    {
        $overdueInvoices = Invoice::where('payment_status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $invoice->calculateLateFee();
        }

        $this->info('Late fees calculated successfully.');
    }
}
