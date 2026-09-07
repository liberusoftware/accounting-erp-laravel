<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MigrationFramework\Exceptions\InvalidMigration;
use Liberu\Accounting\MigrationFramework\Models\MigrationMapping;
use Liberu\Accounting\MigrationFramework\Models\MigrationSource;

final class CreateMapping
{
    public function handle(MigrationSource $source, array $attributes): MigrationMapping
    {
        $ref = trim((string) ($attributes['mapping_ref'] ?? ''));
        $map = $attributes['field_map'] ?? null;
        if ($ref === '' || blank($attributes['entity_type'] ?? null) || ! is_array($map) || $map === []) {
            throw new InvalidMigration('A mapping requires reference, entity type, and fields.');
        }

        return DB::transaction(fn (): MigrationMapping => MigrationMapping::create(['source_id' => $source->id, 'mapping_ref' => $ref, 'entity_type' => $attributes['entity_type'], 'field_map' => $map, 'transforms' => $attributes['transforms'] ?? null, 'validation_rules' => $attributes['validation_rules'] ?? null, 'version' => $attributes['version'] ?? 1, 'active' => $attributes['active'] ?? true, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
