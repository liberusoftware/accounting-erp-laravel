<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollIntegration\Enums\ImportStatus;
use Liberu\Accounting\PayrollIntegration\Exceptions\InvalidPayrollImport;
use Liberu\Accounting\PayrollIntegration\Models\PayrollImport;

final class ImportPayrollRun
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): PayrollImport
    {
        $provider = trim((string) ($attributes['provider'] ?? ''));
        $run = trim((string) ($attributes['run_ref'] ?? ''));
        $start = (string) ($attributes['period_start'] ?? '');
        $end = (string) ($attributes['period_end'] ?? '');
        if ($provider === '' || $run === '' || $start === '' || $end === '') {
            throw new InvalidPayrollImport('Provider, period, and run identity are required.');
        }if ($end < $start) {
            throw new InvalidPayrollImport('Payroll period is invalid.');
        }$errors = $this->validatePayload($attributes);
        $status = $errors === [] ? ImportStatus::Validated : ImportStatus::Failed;

        return DB::transaction(function () use ($attributes, $provider, $run, $errors, $status): PayrollImport {
            $row = PayrollImport::query()->firstOrNew(['team_id' => $attributes['team_id'] ?? null, 'provider' => $provider, 'run_ref' => $run]);
            $row->fill(array_merge($attributes, ['provider' => $provider, 'run_ref' => $run, 'payload_hash' => hash('sha256', json_encode($attributes, JSON_THROW_ON_ERROR)), 'validation_errors' => $errors, 'status' => $status]));
            $row->save();

            return $row;
        });
    }

    /** @param array<string,mixed> $attributes @return array<int,string> */
    private function validatePayload(array $attributes): array
    {
        $errors = [];
        if (! is_array($attributes['employee_refs'] ?? []) && ! is_array($attributes['contractor_refs'] ?? [])) {
            $errors[] = 'At least one employee or contractor reference is required.';
        }foreach (['employee_refs', 'contractor_refs', 'dimensions', 'project_refs'] as $field) {
            if (isset($attributes[$field]) && ! is_array($attributes[$field])) {
                $errors[] = $field.' must be an array.';
            }
        }

        return $errors;
    }
}
