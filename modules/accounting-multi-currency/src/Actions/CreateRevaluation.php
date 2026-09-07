<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MultiCurrency\Enums\GainStatus;
use Liberu\Accounting\MultiCurrency\Enums\RevaluationStatus;
use Liberu\Accounting\MultiCurrency\Events\RevaluationCalculated;
use Liberu\Accounting\MultiCurrency\Exceptions\InvalidCurrency;
use Liberu\Accounting\MultiCurrency\Models\RevaluationRun;

final class CreateRevaluation
{
    /** @param array<string,mixed> $attributes @param array<int,array<string,mixed>> $positions */
    public function handle(array $attributes, array $positions): RevaluationRun
    {
        foreach (['run_ref', 'as_of_date', 'functional_currency'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidCurrency("Revaluation field [{$key}] is required.");
            }
        }
        if ($positions === []) {
            throw new InvalidCurrency('A revaluation requires at least one foreign-currency position.');
        }
        $functional = strtoupper((string) $attributes['functional_currency']);
        $hash = hash('sha256', json_encode(['attributes' => $attributes, 'positions' => $positions], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        return DB::transaction(function () use ($attributes, $positions, $functional, $hash): RevaluationRun {
            $existing = RevaluationRun::query()->where(['team_id' => $attributes['team_id'] ?? null, 'run_ref' => $attributes['run_ref']])->first();
            if ($existing) {
                if ($existing->source_hash !== $hash) {
                    throw new InvalidCurrency('Revaluation reference already exists with different source data.');
                }

                return $existing->load('positions');
            }
            $run = RevaluationRun::create(['team_id' => $attributes['team_id'] ?? null, 'run_ref' => $attributes['run_ref'], 'scope_ref' => $attributes['scope_ref'] ?? null, 'as_of_date' => $attributes['as_of_date'], 'functional_currency' => $functional, 'status' => RevaluationStatus::Calculated, 'source_hash' => $hash, 'requested_by' => $attributes['requested_by'] ?? null]);
            $gains = $losses = $realizedGains = $realizedLosses = 0.0;
            foreach ($positions as $position) {
                $currency = strtoupper((string) ($position['currency'] ?? ''));
                $foreign = (float) ($position['foreign_amount'] ?? 0);
                $book = (float) ($position['book_rate'] ?? 0);
                $closing = (float) ($position['closing_rate'] ?? 0);
                if (! preg_match('/^[A-Z]{3}$/', $currency) || $currency === $functional || abs($foreign) < 0.0000001 || $book <= 0 || $closing <= 0) {
                    throw new InvalidCurrency('Each position needs a distinct currency, amount, and positive book/closing rates.');
                }
                $bookValue = round($foreign * $book, 2);
                $closingValue = round($foreign * $closing, 2);
                $gainLoss = round($closingValue - $bookValue, 2);
                $status = ($position['gain_status'] ?? null) === 'realized' ? GainStatus::Realized : GainStatus::Unrealized;
                $run->positions()->create(['reference_type' => $position['reference_type'] ?? 'balance', 'reference_id' => (string) ($position['reference_id'] ?? ''), 'currency' => $currency, 'foreign_amount' => $foreign, 'book_rate' => $book, 'closing_rate' => $closing, 'book_value' => $bookValue, 'closing_value' => $closingValue, 'gain_loss' => $gainLoss, 'gain_status' => $status, 'metadata' => $position['metadata'] ?? null]);
                if ($status === GainStatus::Realized) {
                    if ($gainLoss >= 0) {
                        $realizedGains += $gainLoss;
                    } else {
                        $realizedLosses += abs($gainLoss);
                    }
                } elseif ($gainLoss >= 0) {
                    $gains += $gainLoss;
                } else {
                    $losses += abs($gainLoss);
                }
            }
            $run->update(['realized_gain' => $realizedGains, 'realized_loss' => $realizedLosses, 'unrealized_gain' => $gains, 'unrealized_loss' => $losses, 'summary' => ['position_count' => count($positions), 'net_unrealized' => round($gains - $losses, 2), 'net_realized' => round($realizedGains - $realizedLosses, 2)]]);
            $result = $run->refresh()->load('positions');
            DB::afterCommit(fn () => event(new RevaluationCalculated($result)));

            return $result;
        });
    }
}
