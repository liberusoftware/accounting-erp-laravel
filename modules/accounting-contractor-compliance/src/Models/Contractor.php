<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\ContractorCompliance\Enums\ContractorComplianceStatus;

final class Contractor extends Model
{
    protected $table = 'accounting_contractors';

    protected $fillable = ['team_id', 'contractor_ref', 'legal_name', 'classification', 'status', 'withholding_scheme', 'deductions', 'evidence', 'statement', 'regional_export'];

    protected $casts = ['status' => ContractorComplianceStatus::class, 'deductions' => 'array', 'evidence' => 'array', 'statement' => 'array', 'regional_export' => 'array'];
}
