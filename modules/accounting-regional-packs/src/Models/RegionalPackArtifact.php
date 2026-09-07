<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacks\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\RegionalPacks\Enums\RegionalArtifactType;

/**
 * @property int $id
 * @property int $pack_id
 * @property RegionalArtifactType $type
 * @property string $key
 * @property array<string,mixed> $definition
 * @property string $status
 */
final class RegionalPackArtifact extends Model
{
    protected $table = 'accounting_regional_pack_artifacts';

    protected $fillable = ['pack_id', 'type', 'key', 'definition', 'status', 'test_results', 'metadata'];

    protected $casts = ['type' => RegionalArtifactType::class, 'definition' => 'array', 'test_results' => 'array', 'metadata' => 'array'];
}
