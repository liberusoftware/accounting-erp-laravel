<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Intercompany\Enums\ConfirmationStatus;
use Liberu\Accounting\Intercompany\Enums\TransactionStatus;
use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;

final class ConfirmTransaction
{
    public function handle(IntercompanyTransaction $transaction, string $entity, bool $confirmed, ?string $comment = null): IntercompanyTransaction
    {
        if ($transaction->status !== TransactionStatus::PendingConfirmation) {
            throw new InvalidIntercompany('Only pending transactions can be confirmed.');
        }if (! $confirmed && blank($comment)) {
            throw new InvalidIntercompany('A rejected confirmation requires a comment.');
        }

        return DB::transaction(function () use ($transaction, $entity, $confirmed, $comment): IntercompanyTransaction {
            $transaction->confirmations()->create(['entity_ref' => $entity, 'status' => $confirmed ? ConfirmationStatus::Confirmed : ConfirmationStatus::Rejected, 'confirmed_amount' => $transaction->amount, 'comment' => $comment, 'actor_ref' => $entity, 'confirmed_at' => now()]);
            $transaction->update(['status' => $confirmed ? TransactionStatus::Confirmed : TransactionStatus::Disputed]);

            return $transaction->refresh();
        });
    }
}
