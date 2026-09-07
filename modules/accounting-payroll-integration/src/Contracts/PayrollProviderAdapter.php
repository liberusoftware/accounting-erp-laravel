<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegration\Contracts;

interface PayrollProviderAdapter
{
    /** @return array<string,mixed> */
    public function validateImport(array $payload): array;

    /** @return array<string,mixed> */
    public function import(array $payload): array;
}
