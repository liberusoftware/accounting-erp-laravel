<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Erp\Database\Factories\ReconciliationRuleFactory;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

#[UseFactory(ReconciliationRuleFactory::class)]
class ReconciliationRule extends Model
{
    use HasFactory;
    use IsTenantModel;

    #[\Override]
    protected $fillable = [
        'team_id',
        'name',
        'match_field',
        'match_operator',
        'match_value',
        'match_value_secondary',
        'action_account_id',
        'priority',
        'is_active',
    ];

    #[\Override]
    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function actionAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'action_account_id');
    }
}
