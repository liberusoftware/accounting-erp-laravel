<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\Erp\Models\Expense;
use Liberu\Accounting\Erp\Models\Invoice;

class ProcessRecurringTransactions extends Command
{
    #[\Override]
    protected $signature = 'recurring:process';

    #[\Override]
    protected $description = 'Generate draft occurrences for all due recurring invoices, bills, and expenses';

    public function handle(): void
    {
        $total = 0;

        foreach ([Invoice::class, Bill::class, Expense::class] as $model) {
            $model::where('is_recurring', true)->each(function ($template) use (&$total): void {
                try {
                    $total += $template->generateDue();
                } catch (\Throwable $e) {
                    // One bad template must not halt the rest.
                    Log::error('Recurring generation failed for template.', [
                        'model' => $template::class,
                        'id' => $template->getKey(),
                        'error' => $e->getMessage(),
                    ]);
                }
            });
        }

        $this->info("Generated {$total} recurring document(s).");
    }
}
