<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $source_id
 * @property array<string,mixed>|null $transforms
 */
final class MigrationMapping extends Model
{
    protected $table = 'accounting_migration_mappings';

    protected $fillable = ['source_id', 'mapping_ref', 'entity_type', 'field_map', 'transforms', 'validation_rules', 'version', 'active', 'metadata'];

    protected $casts = ['field_map' => 'array', 'transforms' => 'array', 'validation_rules' => 'array', 'version' => 'integer', 'active' => 'boolean', 'metadata' => 'array'];

    public function source(): BelongsTo
    {
        return $this->belongsTo(MigrationSource::class, 'source_id');
    }
}
