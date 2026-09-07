<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TradingRule extends Model
{
    protected $table = 'accounting_intercompany_trading_rules';

    protected $fillable = ['counterparty_id', 'rule_ref', 'description', 'pricing_method', 'markup_percent', 'currency', 'active', 'metadata'];

    protected $casts = ['markup_percent' => 'decimal:4', 'active' => 'boolean', 'metadata' => 'array'];

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(IntercompanyCounterparty::class, 'counterparty_id');
    }
}
