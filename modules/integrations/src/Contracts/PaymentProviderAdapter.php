<?php

declare(strict_types=1);

namespace Liberu\Foundation\Integrations\Contracts;

interface PaymentProviderAdapter
{
    /** @param array<string, mixed> $payment */
    public function sendPayment(array $payment): array;

    /** @param list<array<string, mixed>> $payments */
    public function sendBulkPayments(string $title, array $payments, ?string $scheduleFor = null): array;
}
