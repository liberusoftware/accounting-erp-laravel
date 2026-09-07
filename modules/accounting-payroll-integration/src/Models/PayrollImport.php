<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegration\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\PayrollIntegration\Enums\ImportStatus;

/**
 * @property int $id
 * @property ImportStatus $status
 * @property string $provider
 * @property string $run_ref
 * @property CarbonInterface|null $imported_at
 * @property CarbonInterface|null $reconciled_at
 */
final class PayrollImport extends Model
{
    protected $table = 'accounting_payroll_imports';

    protected $fillable = ['team_id', 'provider', 'period_start', 'period_end', 'run_ref', 'currency', 'employee_refs', 'contractor_refs', 'dimensions', 'project_refs', 'payload_hash', 'validation_errors', 'adapter_ref', 'status', 'imported_at', 'reconciled_at', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'employee_refs' => 'array', 'contractor_refs' => 'array', 'dimensions' => 'array', 'project_refs' => 'array', 'validation_errors' => 'array', 'status' => ImportStatus::class, 'imported_at' => 'datetime', 'reconciled_at' => 'datetime', 'metadata' => 'array'];
}
