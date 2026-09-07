<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MigrationSource extends Model
{
    protected $table = 'accounting_migration_sources';

    protected $fillable = ['team_id', 'source_ref', 'provider', 'source_type', 'name', 'record_count', 'checksum', 'status', 'metadata'];

    protected $casts = ['record_count' => 'integer', 'metadata' => 'array'];

    public function mappings(): HasMany
    {
        return $this->hasMany(MigrationMapping::class, 'source_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(MigrationBatch::class, 'source_id');
    }
}
