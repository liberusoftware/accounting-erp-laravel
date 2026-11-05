<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Services\PaymentPostingService;
use RuntimeException;

class PostPayment extends Command
{
    #[\Override]
    protected $signature = 'payments:post {payment : Payment ID}';

    #[\Override]
    protected $description = 'Post a payment to the general ledger (Dr Cash / Cr AR)';

    public function handle(PaymentPostingService $service): int
    {
        $payment = Payment::find($this->argument('payment'));
        if (! $payment instanceof Payment) {
            $this->error("Payment {$this->argument('payment')} not found.");

            return self::FAILURE;
        }

        $id = (int) $payment->getKey();

        if ($payment->journal_entry_id !== null) {
            $this->info("Payment {$id} already posted (entry {$payment->journal_entry_id}); skipped.");

            return self::SUCCESS;
        }

        try {
            $entry = $service->post($payment);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Posted payment {$id} to ledger (entry {$entry->id}).");

        return self::SUCCESS;
    }
}
