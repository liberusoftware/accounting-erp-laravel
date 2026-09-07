<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\TaxCore\Enums\TaxRuleStatus;
use Liberu\Accounting\TaxCore\Enums\TaxTreatment;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $tax_type
 * @property string|null $jurisdiction_code
 * @property float|string $rate
 * @property TaxTreatment $treatment
 * @property Carbon $effective_from
 * @property Carbon|null $effective_until
 * @property TaxRuleStatus $status
 * @property string|null $exemption_code
 * @property string|null $control_account_code
 * @property string $rounding_method
 * @property int $rounding_scale
 * @property array<string,mixed>|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, TaxEvidence> $evidence
 */
final class TaxRule extends Model
{
    protected $table = 'accounting_tax_rules';

    protected $fillable = ['code', 'name', 'tax_type', 'jurisdiction_code', 'rate', 'treatment', 'effective_from', 'effective_until', 'status', 'exemption_code', 'control_account_code', 'rounding_method', 'rounding_scale', 'metadata'];

    protected $casts = ['rate' => 'decimal:6', 'treatment' => TaxTreatment::class, 'effective_from' => 'date', 'effective_until' => 'date', 'status' => TaxRuleStatus::class, 'rounding_scale' => 'integer', 'metadata' => 'array'];

    public function evidence(): HasMany
    {
        return $this->hasMany(TaxEvidence::class, 'tax_rule_id');
    }

    public function scopeActive(Builder $query, ?string $on = null): Builder
    {
        $date = $on ?? now()->toDateString();

        return $query->where('status', TaxRuleStatus::Active->value)->whereDate('effective_from', '<=', $date)->where(fn (Builder $query): Builder => $query->whereNull('effective_until')->orWhereDate('effective_until', '>=', $date));
    }
}
