<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\MultiEntity\Enums\EntityBookStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $entity_ref
 * @property string $code
 * @property string $name
 * @property string $base_currency
 * @property EntityBookStatus $status
 */
final class EntityBook extends Model
{
    protected $table = 'accounting_multi_entity_books';

    protected $fillable = ['team_id', 'entity_ref', 'code', 'name', 'base_currency', 'timezone', 'tax_registration', 'status', 'metadata'];

    protected $casts = ['status' => EntityBookStatus::class, 'metadata' => 'array'];

    /** @return HasMany<EntityAccess, $this> */
    public function access(): HasMany
    {
        return $this->hasMany(EntityAccess::class, 'entity_id');
    }

    /** @return HasMany<MasterDataPolicy, $this> */
    public function policies(): HasMany
    {
        return $this->hasMany(MasterDataPolicy::class, 'entity_id');
    }

    /** @return HasMany<EntityPeriod, $this> */
    public function periods(): HasMany
    {
        return $this->hasMany(EntityPeriod::class, 'entity_id');
    }

    /** @return HasMany<EntityMapping, $this> */
    public function mappings(): HasMany
    {
        return $this->hasMany(EntityMapping::class, 'entity_id');
    }
}
