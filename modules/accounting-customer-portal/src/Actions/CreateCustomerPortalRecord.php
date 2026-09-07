<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal\Actions;

use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalRecordType;
use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalStatus;
use Liberu\Accounting\CustomerPortal\Exceptions\InvalidCustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Models\CustomerPortalRecord;

final class CreateCustomerPortalRecord
{
    public function handle(array $attributes): CustomerPortalRecord
    {
        $type = $attributes['type'] ?? null;
        $type = $type instanceof CustomerPortalRecordType ? $type->value : $type;
        if (! is_string($type) || ! in_array($type, array_column(CustomerPortalRecordType::cases(), 'value'), true)) {
            throw new InvalidCustomerPortalRecord('A valid customer portal record type is required.');
        } foreach (['team_id', 'customer_id', 'reference'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCustomerPortalRecord("{$field} is required.");
            }
        } if ((float) ($attributes['amount'] ?? 0) < 0) {
            throw new InvalidCustomerPortalRecord('Amount must not be negative.');
        } if (CustomerPortalRecord::query()->where(['team_id' => $attributes['team_id'], 'type' => $type, 'reference' => $attributes['reference']])->exists()) {
            throw new InvalidCustomerPortalRecord('The portal reference already exists.');
        }

        return CustomerPortalRecord::create([...$attributes, 'type' => $type, 'status' => $attributes['status'] ?? CustomerPortalStatus::Draft]);
    }
}
