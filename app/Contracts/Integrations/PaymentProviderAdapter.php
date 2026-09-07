<?php

declare(strict_types=1);

namespace Liberu\Foundation\Integrations\Contracts;

/**
 * Compatibility contract for provider packages that implement payments.
 *
 * The published integrations package may lag the application modules, so the
 * application keeps this small contract available during clean installs.
 */
interface PaymentProviderAdapter
{
    /** @param array<string, mixed> $payment */
    public function sendPayment(array $payment): array;

    /** @param list<array<string, mixed>> $payments */
    public function sendBulkPayments(string $title, array $payments, ?string $scheduleFor = null): array;
}
