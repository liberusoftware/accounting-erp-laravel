<?php

namespace Liberu\Accounting\Erp\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use InvalidArgumentException;
use Liberu\Accounting\Erp\Models\QboConnection;
use Liberu\Accounting\Erp\Models\SageConnection;
use Liberu\Accounting\Erp\Models\XeroConnection;
use Liberu\Accounting\Erp\Services\QuickBooksService;
use Liberu\Accounting\Erp\Services\SageService;
use Liberu\Accounting\Erp\Services\XeroService;

class SyncAccountingProviderConnectionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public int $timeout = 300;

    public function __construct(public readonly string $provider, public readonly int $connectionId)
    {
        $this->afterCommit();
    }

    public function handle(QuickBooksService $qbo, XeroService $xero, SageService $sage): void
    {
        $connection = match ($this->provider) {
            'qbo' => QboConnection::query()->find($this->connectionId),
            'xero' => XeroConnection::query()->find($this->connectionId),
            'sage' => SageConnection::query()->find($this->connectionId),
            default => throw new InvalidArgumentException('Unsupported accounting provider: '.$this->provider),
        };
        if ($connection === null || $connection->getAttribute('status') !== 'active') {
            return;
        }

        match (true) {
            $connection instanceof QboConnection => $qbo->sync($connection),
            $connection instanceof XeroConnection => $xero->sync($connection),
            $connection instanceof SageConnection => $sage->sync($connection),
        };
    }
}
