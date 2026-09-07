<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournals\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\PayrollJournals\Enums\JournalStatus;

/**
 * @property int $id
 * @property JournalStatus $status
 * @property float|string $gross_pay
 * @property float|string $net_pay
 */
final class PayrollJournal extends Model
{
    protected $table = 'accounting_payroll_journals';

    protected $fillable = ['team_id', 'journal_ref', 'payroll_period_start', 'payroll_period_end', 'currency', 'gross_pay', 'taxes', 'deductions', 'benefits', 'employer_costs', 'net_pay', 'liabilities', 'allocation', 'status', 'posted_at', 'reversed_at', 'reversal_ref', 'correction_ref', 'metadata'];

    protected $casts = ['payroll_period_start' => 'date', 'payroll_period_end' => 'date', 'gross_pay' => 'decimal:2', 'taxes' => 'decimal:2', 'deductions' => 'decimal:2', 'benefits' => 'decimal:2', 'employer_costs' => 'decimal:2', 'net_pay' => 'decimal:2', 'liabilities' => 'array', 'allocation' => 'array', 'status' => JournalStatus::class, 'posted_at' => 'datetime', 'reversed_at' => 'datetime', 'metadata' => 'array'];
}
