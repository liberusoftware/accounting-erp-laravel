<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCoding\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\CashCoding\Enums\CashCodingStatus;
use Liberu\Accounting\CashCoding\Exceptions\InvalidCashCoding;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;

final class CreateCashCodingBatch
{
    public function handle(array $attributes): CashCodingBatch
    {
        $teamId = (int) ($attributes['team_id'] ?? 0);
        $reference = trim((string) ($attributes['reference'] ?? ''));
        $lines = $attributes['lines'] ?? [];
        $currency = strtoupper(trim((string) ($attributes['currency'] ?? '')));
        if ($teamId < 1 || $reference === '' || ! is_array($lines) || $lines === [] || count($lines) > 500 || ! preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new InvalidCashCoding('A cash coding batch requires a reference, currency, and between 1 and 500 lines.');
        }
        $total = 0.0;
        foreach ($lines as $line) {
            if (! is_array($line) || blank($line['source_reference'] ?? null) || ! is_numeric($line['amount'] ?? null) || (float) $line['amount'] === 0 || strtoupper((string) ($line['currency'] ?? $currency)) !== $currency || blank($line['account_id'] ?? null)) {
                throw new InvalidCashCoding('Every coding line requires a source reference, non-zero amount, matching currency, and account.');
            }
            $total += (float) $line['amount'];
        }
        if (CashCodingBatch::query()->where(['team_id' => $teamId, 'reference' => $reference])->exists()) {
            throw new InvalidCashCoding('This cash coding batch reference already exists.');
        }

        return DB::transaction(fn (): CashCodingBatch => CashCodingBatch::query()->create(array_merge($attributes, ['team_id' => $teamId, 'reference' => $reference, 'currency' => $currency, 'lines' => array_values($lines), 'total_amount' => $total, 'status' => CashCodingStatus::Draft]))->refresh());
    }
}
