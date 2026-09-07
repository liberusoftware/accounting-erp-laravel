<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccounts\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\ChartOfAccounts\Enums\AccountType;
use Liberu\Accounting\ChartOfAccounts\Enums\NormalBalance;
use Liberu\Accounting\Core\Models\LegalEntity;

/**
 * @property int $legal_entity_id
 * @property string $code
 * @property string $name
 * @property AccountType|null $type
 * @property int|null $parent_id
 * @property bool $allow_manual_entry
 * @property bool $is_active
 */
class Account extends Model
{
    protected $table = 'accounting_chart_accounts';

    protected $fillable = [
        'legal_entity_id', 'code', 'name', 'description', 'type', 'normal_balance',
        'parent_id', 'is_control_account', 'allow_manual_entry', 'is_active', 'locale', 'metadata',
    ];

    protected $attributes = [
        'is_control_account' => false,
        'allow_manual_entry' => true,
        'is_active' => true,
    ];

    protected $casts = [
        'type' => AccountType::class,
        'normal_balance' => NormalBalance::class,
        'is_control_account' => 'bool',
        'allow_manual_entry' => 'bool',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function legalEntity(): BelongsTo
    {
        return $this->belongsTo(LegalEntity::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
