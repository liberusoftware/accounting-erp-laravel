<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollJournals\Enums\JournalStatus;
use Liberu\Accounting\PayrollJournals\Exceptions\InvalidPayrollJournal;
use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;

final class CreatePayrollJournal
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): PayrollJournal
    {
        $gross = (float) ($attributes['gross_pay'] ?? 0);
        $taxes = (float) ($attributes['taxes'] ?? 0);
        $deductions = (float) ($attributes['deductions'] ?? 0);
        $benefits = (float) ($attributes['benefits'] ?? 0);
        $employer = (float) ($attributes['employer_costs'] ?? 0);
        foreach ([$gross, $taxes, $deductions, $benefits, $employer] as $value) {
            if ($value < 0) {
                throw new InvalidPayrollJournal('Payroll journal amounts cannot be negative.');
            }
        }$net = (float) ($attributes['net_pay'] ?? ($gross - $taxes - $deductions));
        if ($net < 0 || abs($net - ($gross - $taxes - $deductions)) > 0.01) {
            throw new InvalidPayrollJournal('Net pay must equal gross pay less taxes and deductions.');
        }if (blank($attributes['journal_ref'] ?? null) || $gross <= 0) {
            throw new InvalidPayrollJournal('Journal reference and positive gross pay are required.');
        }

        return DB::transaction(function () use ($attributes, $gross, $taxes, $deductions, $benefits, $employer, $net): PayrollJournal {
            $row = PayrollJournal::query()->firstOrNew(['team_id' => $attributes['team_id'] ?? null, 'journal_ref' => $attributes['journal_ref']]);
            $row->fill(array_merge($attributes, ['gross_pay' => $gross, 'taxes' => $taxes, 'deductions' => $deductions, 'benefits' => $benefits, 'employer_costs' => $employer, 'net_pay' => $net, 'status' => $attributes['status'] ?? JournalStatus::Draft]));
            $row->save();

            return $row;
        });
    }
}
