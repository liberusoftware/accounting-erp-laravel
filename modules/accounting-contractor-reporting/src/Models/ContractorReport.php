<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\ContractorReporting\Enums\ContractorReportStatus;

final class ContractorReport extends Model
{
    protected $table = 'accounting_contractor_reports';

    protected $fillable = ['team_id', 'payee_ref', 'tax_year', 'classification', 'threshold', 'reportable_amount', 'form_type', 'status', 'filing_adapter', 'payee_validation', 'correction', 'evidence'];

    protected $casts = ['threshold' => 'decimal:8', 'reportable_amount' => 'decimal:8', 'status' => ContractorReportStatus::class, 'payee_validation' => 'array', 'correction' => 'array', 'evidence' => 'array'];
}
