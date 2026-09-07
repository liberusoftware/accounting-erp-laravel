<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\DebtAndLoans\Enums\DebtMovementKind;
use Liberu\Accounting\DebtAndLoans\Enums\DebtMovementStatus;
use Liberu\Accounting\DebtAndLoans\Exceptions\InvalidDebt;
use Liberu\Accounting\DebtAndLoans\Models\DebtFacility;
use Liberu\Accounting\DebtAndLoans\Models\DebtMovement;

final class RecordDebtMovement
{
    public function handle(DebtFacility $facility, string|DebtMovementKind $kind, array $attributes): DebtMovement
    {
        $movementKind = $kind instanceof DebtMovementKind ? $kind->value : $kind;
        if (! in_array($movementKind, array_column(DebtMovementKind::cases(), 'value'), true) || blank($attributes['movement_date'] ?? null)) {
            throw new InvalidDebt('A valid movement kind and date are required.');
        }
        $principal = (float) ($attributes['principal_amount'] ?? 0);
        $interest = (float) ($attributes['interest_amount'] ?? 0);
        $fee = (float) ($attributes['fee_amount'] ?? 0);
        if ($principal < 0 || $interest < 0 || $fee < 0 || ($principal + $interest + $fee) <= 0) {
            throw new InvalidDebt('Debt movement amounts must be non-negative and non-zero.');
        }
        if ($movementKind === DebtMovementKind::Drawdown->value && (float) $facility->drawn_amount + $principal > (float) $facility->limit_amount) {
            throw new InvalidDebt('Drawdown exceeds the facility limit.');
        }
        if ($movementKind === DebtMovementKind::Repayment->value && $principal > (float) $facility->drawn_amount) {
            throw new InvalidDebt('Repayment exceeds the outstanding principal.');
        }

        return DB::transaction(function () use ($facility, $movementKind, $attributes, $principal, $interest, $fee): DebtMovement {
            $movement = $facility->movements()->create([...$attributes, 'team_id' => $facility->team_id, 'kind' => $movementKind, 'principal_amount' => $principal, 'interest_amount' => $interest, 'fee_amount' => $fee, 'status' => DebtMovementStatus::Scheduled]);
            if ($movementKind === DebtMovementKind::Drawdown->value) {
                $facility->increment('drawn_amount', $principal);
            }
            if ($movementKind === DebtMovementKind::Repayment->value) {
                $facility->decrement('drawn_amount', $principal);
            }

            return $movement;
        });
    }
}
