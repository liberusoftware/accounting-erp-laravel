<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments\Actions;

use Liberu\Accounting\CreditNotesAndAdjustments\Enums\CreditNoteStatus;
use Liberu\Accounting\CreditNotesAndAdjustments\Exceptions\InvalidCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Models\CreditNote;

final class CreateCreditNote
{
    public function handle(array $attributes): CreditNote
    {
        foreach (['team_id', 'customer_id', 'credit_ref', 'reason', 'currency', 'amount'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCreditNote("{$field} is required.");
            }
        } if ((float) $attributes['amount'] <= 0) {
            throw new InvalidCreditNote('Credit amount must be positive.');
        } if (CreditNote::query()->where(['team_id' => $attributes['team_id'], 'credit_ref' => $attributes['credit_ref']])->exists()) {
            throw new InvalidCreditNote('Credit reference already exists.');
        }

        return CreditNote::create([...$attributes, 'status' => CreditNoteStatus::Draft, 'allocated_amount' => 0]);
    }
}
