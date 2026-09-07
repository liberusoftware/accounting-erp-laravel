<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Models;

use Illuminate\Database\Eloquent\Model;

final class ExchangeRate extends Model
{
    protected $table = 'accounting_multi_currency_rates';

    protected $fillable = ['team_id', 'from_currency', 'to_currency', 'rate_date', 'rate', 'source', 'rate_type', 'is_historical', 'metadata'];

    protected $casts = ['rate_date' => 'date', 'rate' => 'decimal:10', 'is_historical' => 'boolean', 'metadata' => 'array'];
}
