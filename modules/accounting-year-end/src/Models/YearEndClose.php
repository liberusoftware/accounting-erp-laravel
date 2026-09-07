<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;

final class YearEndClose extends Model
{
    protected $table = 'accounting_year_end_closes';

    protected $fillable = ['team_id', 'fiscal_year', 'period_end', 'retained_earnings_account_ref', 'net_income', 'status', 'closed_at', 'locked_at', 'closing_entry_ref', 'metadata'];

    protected $casts = ['period_end' => 'date', 'net_income' => 'decimal:6', 'closed_at' => 'datetime', 'locked_at' => 'datetime', 'status' => YearEndStatus::class, 'metadata' => 'array'];
}
