<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MigrationFramework\Exceptions\InvalidMigration;
use Liberu\Accounting\MigrationFramework\Models\MigrationSource;

final class RegisterSource
{
    public function handle(array $attributes): MigrationSource
    {
        $ref = trim((string) ($attributes['source_ref'] ?? ''));
        if ($ref === '' || blank($attributes['provider'] ?? null) || blank($attributes['source_type'] ?? null) || blank($attributes['name'] ?? null)) {
            throw new InvalidMigration('A source requires reference, provider, type, and name.');
        }

        return DB::transaction(function () use ($attributes, $ref): MigrationSource {
            return MigrationSource::updateOrCreate(['team_id' => $attributes['team_id'] ?? null, 'source_ref' => $ref], ['provider' => $attributes['provider'], 'source_type' => $attributes['source_type'], 'name' => $attributes['name'], 'record_count' => (int) ($attributes['record_count'] ?? 0), 'checksum' => $attributes['checksum'] ?? null, 'status' => $attributes['status'] ?? 'active', 'metadata' => $attributes['metadata'] ?? null]);
        });
    }
}
