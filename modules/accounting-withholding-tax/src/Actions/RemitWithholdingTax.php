<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\WithholdingTax\Enums\WithholdingStatus;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxLiability;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxRemittance;

final class RemitWithholdingTax
{
    public function handle(WithholdingTaxLiability $liability, array $attributes): WithholdingTaxRemittance
    {
        return DB::transaction(function () use ($liability, $attributes): WithholdingTaxRemittance {
            $remittance = WithholdingTaxRemittance::create(['team_id' => $liability->team_id, 'liability_id' => $liability->id, 'amount' => $attributes['amount'], 'remitted_on' => $attributes['remitted_on'], 'reference' => $attributes['reference'], 'status' => WithholdingStatus::Remitted, 'metadata' => $attributes['metadata'] ?? null]);
            $liability->update(['status' => WithholdingStatus::Remitted]);

            return $remittance;
        });
    }
}
