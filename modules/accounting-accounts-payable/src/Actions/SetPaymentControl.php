<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsPayable\Events\PaymentControlChanged;
use Liberu\Accounting\AccountsPayable\Exceptions\InvalidPayable;
use Liberu\Accounting\AccountsPayable\Models\PayableAccount;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class SetPaymentControl
{
    public function handle(int $partyId, ?bool $paymentHold = null, ?string $holdReason = null): PayableAccount
    {
        if (! Party::query()->whereKey($partyId)->where('type', PartyType::Supplier)->exists()) {
            throw new InvalidPayable('Payment control can only be configured for an existing supplier.');
        }
        $account = app(EnsureAccount::class)->handle($partyId);
        $account->update(array_filter(['payment_hold' => $paymentHold, 'hold_reason' => $holdReason], static fn ($value): bool => $value !== null));
        DB::afterCommit(fn (): mixed => event(new PaymentControlChanged(PayableAccount::query()->findOrFail($account->getKey()))));

        return $account->refresh();
    }
}
