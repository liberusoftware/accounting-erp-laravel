<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class IntercompanyCounterparty extends Model
{
    protected $table = 'accounting_intercompany_counterparties';

    protected $fillable = ['team_id', 'entity_ref', 'counterparty_ref', 'name', 'default_currency', 'active', 'metadata'];

    protected $casts = ['active' => 'boolean', 'metadata' => 'array'];

    public function rules(): HasMany
    {
        return $this->hasMany(TradingRule::class, 'counterparty_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(IntercompanyTransaction::class, 'counterparty_id');
    }
}
