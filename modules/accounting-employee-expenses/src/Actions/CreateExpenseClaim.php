<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Exceptions\InvalidClaim;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final class CreateExpenseClaim
{
    public function handle(array $a): ExpenseClaim
    {
        foreach (['employee_ref', 'claim_ref', 'currency'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidClaim("Missing claim field [{$k}].");
            }
        }if (! preg_match('/^[A-Za-z]{3}$/', (string) $a['currency'])) {
            throw new InvalidClaim('Currency must be an ISO three-letter code.');
        }

        return DB::transaction(function () use ($a): ExpenseClaim {
            $c = ExpenseClaim::create(['team_id' => $a['team_id'] ?? null, 'employee_ref' => $a['employee_ref'], 'claim_ref' => $a['claim_ref'], 'currency' => strtoupper($a['currency']), 'status' => ClaimStatus::Draft, 'project_ref' => $a['project_ref'] ?? null, 'dimension_ref' => $a['dimension_ref'] ?? null, 'metadata' => $a['metadata'] ?? null]);
            $c->history()->create(['event' => 'created', 'actor_ref' => $a['actor_ref'] ?? null]);

            return $c->refresh();
        });
    }
}
