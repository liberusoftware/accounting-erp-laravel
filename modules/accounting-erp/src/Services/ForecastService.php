<?php

// src/app/Services/ForecastService.php
declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\ForecastScenario;

class ForecastService
{
    private const PL_TYPES = ['revenue', 'cost_of_goods_sold', 'expense'];

    /**
     * @return array<string, array<int, float>>
     */
    public function rollingBaseline(int $periods, ?int $teamId = null): array
    {
        $teamId ??= auth()->user()->current_team_id ?? -1;
        $baseline = [];

        foreach (self::PL_TYPES as $type) {
            $total = 0.0;
            $accounts = Account::query()->where('team_id', $teamId)->where('account_type', $type)->get();
            foreach ($accounts as $account) {
                $avg = $account->transactions()
                    ->orderBy('transaction_date', 'desc')
                    // Deterministic tiebreaker: same-date rows must resolve consistently,
                    // else which 12 rows the LIMIT keeps varies across runs/engines.
                    ->orderBy('transaction_id', 'desc')
                    ->limit(12)
                    ->pluck('amount')
                    ->avg();
                $total += (float) ($avg ?? 0);
            }
            $baseline[$type] = array_fill(1, max($periods, 1), round($total, 2));
        }

        return $baseline;
    }

    /**
     * @param  array<string, array<int, float>>  $baseline
     * @return array<string, array<int, float>>
     */
    public function applyScenario(array $baseline, ForecastScenario $scenario): array
    {
        $factors = $scenario->lines->pluck('adjustment_pct', 'account_type');
        $result = [];
        foreach ($baseline as $type => $periods) {
            $factor = 1 + ((float) ($factors[$type] ?? 0)) / 100;
            $result[$type] = array_map(static fn (float $v): float => round($v * $factor, 2), $periods);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function compare(int $periods, ForecastScenario $scenario, ?int $teamId = null): array
    {
        $baseline = $this->rollingBaseline($periods, $teamId);
        $scenarioProjection = $this->applyScenario($baseline, $scenario);

        return [
            'baseline' => $baseline,
            'scenario' => $scenarioProjection,
            'baseline_net_income' => $this->netIncome($baseline, $periods),
            'scenario_net_income' => $this->netIncome($scenarioProjection, $periods),
        ];
    }

    /**
     * @param  array<string, array<int, float>>  $projection
     * @return array<int, float>
     */
    private function netIncome(array $projection, int $periods): array
    {
        $net = [];
        for ($p = 1; $p <= max($periods, 1); $p++) {
            $net[$p] = round(
                ($projection['revenue'][$p] ?? 0.0)
                    - ($projection['cost_of_goods_sold'][$p] ?? 0.0)
                    - ($projection['expense'][$p] ?? 0.0),
                2,
            );
        }

        return $net;
    }
}
