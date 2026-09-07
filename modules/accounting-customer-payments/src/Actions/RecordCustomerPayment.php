<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Actions;

use Liberu\Accounting\CustomerPayments\Enums\CustomerPaymentKind;
use Liberu\Accounting\CustomerPayments\Enums\CustomerPaymentStatus;
use Liberu\Accounting\CustomerPayments\Exceptions\InvalidCustomerPayment;
use Liberu\Accounting\CustomerPayments\Models\CustomerPayment;

final class RecordCustomerPayment
{
    public function handle(array $attributes): CustomerPayment
    {
        $kind = $attributes['kind'] ?? null;
        $kind = $kind instanceof CustomerPaymentKind ? $kind->value : $kind;
        if (! is_string($kind) || ! in_array($kind, array_column(CustomerPaymentKind::cases(), 'value'), true)) {
            throw new InvalidCustomerPayment('A valid payment kind is required.');
        } foreach (['team_id', 'customer_id', 'reference', 'currency', 'amount'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCustomerPayment("{$field} is required.");
            }
        } if ((float) $attributes['amount'] <= 0) {
            throw new InvalidCustomerPayment('Payment amount must be positive.');
        } if (CustomerPayment::query()->where(['team_id' => $attributes['team_id'], 'kind' => $kind, 'reference' => $attributes['reference']])->exists()) {
            throw new InvalidCustomerPayment('Payment reference already exists.');
        }

        return CustomerPayment::create([...$attributes, 'kind' => $kind, 'status' => CustomerPaymentStatus::Unreconciled, 'allocated_amount' => 0, 'refunded_amount' => 0]);
    }
}
