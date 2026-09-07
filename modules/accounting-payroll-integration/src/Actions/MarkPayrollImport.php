<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollIntegration\Enums\ImportStatus;
use Liberu\Accounting\PayrollIntegration\Exceptions\InvalidPayrollImport;
use Liberu\Accounting\PayrollIntegration\Models\PayrollImport;

final class MarkPayrollImport
{
    public function handle(PayrollImport $import, ImportStatus $status): PayrollImport
    {
        if ($status === ImportStatus::Imported && $import->status !== ImportStatus::Validated) {
            throw new InvalidPayrollImport('Only validated imports can be marked imported.');
        }if ($status === ImportStatus::Reconciled && $import->status !== ImportStatus::Imported) {
            throw new InvalidPayrollImport('Only imported runs can be reconciled.');
        }

        return DB::transaction(function () use ($import, $status): PayrollImport {
            $import->update(['status' => $status, 'imported_at' => $status === ImportStatus::Imported ? now() : $import->imported_at, 'reconciled_at' => $status === ImportStatus::Reconciled ? now() : $import->reconciled_at]);

            return $import->refresh();
        });
    }
}
