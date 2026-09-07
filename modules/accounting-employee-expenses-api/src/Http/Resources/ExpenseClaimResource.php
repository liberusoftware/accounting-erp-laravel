<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

/** @mixin ExpenseClaim */ final class ExpenseClaimResource extends JsonResource
{
    public function toArray(Request $r): array
    {
        $c = $this->resource;

        return ['id' => $c->getKey(), 'employee_ref' => $c->employee_ref, 'claim_ref' => $c->claim_ref, 'currency' => $c->currency, 'status' => $c->status->value, 'submitted_on' => $c->submitted_on?->toDateString(), 'approved_on' => $c->approved_on?->toDateString(), 'reimbursed_on' => $c->reimbursed_on?->toDateString(), 'posted_on' => $c->posted_on?->toDateString(), 'rejection_reason' => $c->rejection_reason, 'total' => (float) $c->items->sum('amount'), 'items' => $this->whenLoaded('items'), 'history' => $this->whenLoaded('history')];
    }
}
