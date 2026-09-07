<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\XeroMigration\Enums\XeroConnectionStatus;
use Liberu\Accounting\XeroMigration\Exceptions\InvalidXeroMigration;
use Liberu\Accounting\XeroMigration\Models\XeroConnection;

final class ConnectXeroTenant
{
    public function handle(array $attributes): XeroConnection
    {
        if (blank($attributes['tenant_ref'] ?? null) || blank($attributes['access_token'] ?? null)) {
            throw new InvalidXeroMigration('A Xero tenant reference and access token are required.');
        }

        return DB::transaction(fn (): XeroConnection => XeroConnection::updateOrCreate(['team_id' => $attributes['team_id'], 'tenant_ref' => $attributes['tenant_ref']], array_merge($attributes, ['status' => XeroConnectionStatus::Active])));
    }
}
