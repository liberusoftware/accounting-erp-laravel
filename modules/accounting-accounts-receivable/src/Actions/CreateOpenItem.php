<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Enums\ReceivableStatus;
use Liberu\Accounting\AccountsReceivable\Events\OpenItemCreated;
use Liberu\Accounting\AccountsReceivable\Exceptions\InvalidReceivable;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class CreateOpenItem
{
    public function handle(array $attributes): ReceivableOpenItem
    {
        return DB::transaction(function () use ($attributes) {
            $amount = (float) ($attributes['original_amount'] ?? 0);
            if (empty($attributes['party_id']) || blank($attributes['reference'] ?? null) || $amount <= 0 || blank($attributes['currency'] ?? null)) {
                throw new InvalidReceivable('An open item requires a party, reference, positive amount, and currency.');
            }
            if (! Party::query()->whereKey($attributes['party_id'])->where('type', PartyType::Customer)->exists()) {
                throw new InvalidReceivable('Open items can only be created for an existing customer.');
            }
            $item = ReceivableOpenItem::create(array_merge($attributes, ['applied_amount' => 0, 'status' => ReceivableStatus::Open]));
            app(EnsureAccount::class)->handle((int) $item->party_id);
            app(RecalculateAccountBalance::class)->handle((int) $item->party_id);
            $item->refresh();
            DB::afterCommit(fn () => event(new OpenItemCreated($item)));

            return $item;
        });
    }
}
