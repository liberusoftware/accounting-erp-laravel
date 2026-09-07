<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant\Actions;

use Liberu\Accounting\CashCollectionAssistant\Models\CashCollectionAssistant;

final class RecordCollectionPromise
{
    public function handle(CashCollectionAssistant $assistant, string $date, string|float $amount): CashCollectionAssistant
    {
        $assistant->forceFill(['promised_date' => $date, 'promised_amount' => $amount, 'promise_status' => 'open'])->save();

        return $assistant->refresh();
    }
}
