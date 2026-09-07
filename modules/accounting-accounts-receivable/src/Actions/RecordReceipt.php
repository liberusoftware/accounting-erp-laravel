<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Events\ReceiptRecorded;
use Liberu\Accounting\AccountsReceivable\Exceptions\InvalidReceivable;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class RecordReceipt
{
    public function handle(array $attributes): ReceivableReceipt
    {
        return DB::transaction(function () use ($attributes) {
            $amount = (float) ($attributes['amount'] ?? 0);
            if ($amount <= 0 || strlen((string) ($attributes['currency'] ?? '')) !== 3) {
                throw new InvalidReceivable('A receipt requires a positive amount and currency.');
            }
            if (! empty($attributes['party_id']) && ! Party::query()->whereKey($attributes['party_id'])->where('type', PartyType::Customer)->exists()) {
                throw new InvalidReceivable('Receipts can only be assigned to an existing customer.');
            }
            $receipt = ReceivableReceipt::create(array_merge($attributes, ['applied_amount' => 0, 'status' => 'unapplied', 'received_on' => $attributes['received_on'] ?? now()->toDateString()]));
            if (! empty($receipt->party_id)) {
                app(EnsureAccount::class)->handle((int) $receipt->party_id);
            }

            DB::afterCommit(fn (): mixed => event(new ReceiptRecorded($receipt->fresh())));

            return $receipt->refresh();
        });
    }
}
