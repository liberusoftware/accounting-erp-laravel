<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\MultiCurrency\Events\RateRecorded;
use Liberu\Accounting\MultiCurrency\Exceptions\InvalidCurrency;
use Liberu\Accounting\MultiCurrency\Models\ExchangeRate;

final class RecordExchangeRate
{
    public function handle(array $attributes): ExchangeRate
    {
        $from = strtoupper((string) ($attributes['from_currency'] ?? ''));
        $to = strtoupper((string) ($attributes['to_currency'] ?? ''));
        $rate = (float) ($attributes['rate'] ?? 0);
        if (! preg_match('/^[A-Z]{3}$/', $from) || ! preg_match('/^[A-Z]{3}$/', $to) || $from === $to || $rate <= 0 || blank($attributes['rate_date'] ?? null)) {
            throw new InvalidCurrency('Distinct ISO currencies, positive rate, and rate date are required.');
        }

        return DB::transaction(function () use ($attributes, $from, $to, $rate): ExchangeRate {
            $record = ExchangeRate::updateOrCreate(['team_id' => $attributes['team_id'] ?? null, 'from_currency' => $from, 'to_currency' => $to, 'rate_date' => $attributes['rate_date'], 'rate_type' => $attributes['rate_type'] ?? 'spot'], ['rate' => $rate, 'source' => $attributes['source'] ?? 'manual', 'is_historical' => $attributes['is_historical'] ?? true, 'metadata' => $attributes['metadata'] ?? null]);
            DB::afterCommit(fn () => event(new RateRecorded($record->refresh())));

            return $record;
        });
    }
}
