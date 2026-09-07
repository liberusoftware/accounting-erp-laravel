<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Actions;

use Liberu\Accounting\CorporateCards\Exceptions\InvalidCorporateCard;
use Liberu\Accounting\CorporateCards\Models\CardAccount;

final class CreateCardAccount
{
    public function handle(array $attributes): CardAccount
    {
        foreach (['team_id', 'card_ref', 'holder_ref', 'currency', 'limit_amount'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCorporateCard("{$field} is required.");
            }
        } if ((float) $attributes['limit_amount'] <= 0) {
            throw new InvalidCorporateCard('Card limit must be positive.');
        } if (CardAccount::query()->where(['team_id' => $attributes['team_id'], 'card_ref' => $attributes['card_ref']])->exists()) {
            throw new InvalidCorporateCard('Card reference already exists.');
        }

        return CardAccount::create([...$attributes, 'spent_amount' => 0, 'status' => 'active']);
    }
}
