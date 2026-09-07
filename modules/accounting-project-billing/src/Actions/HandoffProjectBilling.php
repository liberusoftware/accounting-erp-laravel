<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBilling\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProjectBilling\Enums\BillingStatus;
use Liberu\Accounting\ProjectBilling\Exceptions\InvalidBilling;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;

final class HandoffProjectBilling
{
    public function handle(ProjectBilling $billing, ?string $invoiceRef = null): ProjectBilling
    {
        if ($billing->status === BillingStatus::Cancelled) {
            throw new InvalidBilling('Cancelled billing cannot be handed off.');
        }

        return DB::transaction(function () use ($billing, $invoiceRef): ProjectBilling {
            $billing->update(['status' => BillingStatus::HandedOff, 'invoice_ref' => $invoiceRef ?? $billing->invoice_ref]);

            return $billing->refresh();
        });
    }
}
