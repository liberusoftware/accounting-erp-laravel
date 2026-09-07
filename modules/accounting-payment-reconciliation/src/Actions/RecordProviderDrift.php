<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PaymentReconciliation\Enums\DriftStatus;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementStatus;
use Liberu\Accounting\PaymentReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\PaymentReconciliation\Models\ProviderDrift;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;

final class RecordProviderDrift
{
    public function handle(SettlementRun $run, string $field, mixed $expected, mixed $actual, string $severity = 'warning'): ProviderDrift
    {
        if (blank($field) || $expected === $actual) {
            throw new InvalidReconciliation('A drift requires a changed provider value and field.');
        }

        return DB::transaction(function () use ($run, $field, $expected, $actual, $severity): ProviderDrift {/** @var ProviderDrift $drift */ $drift = $run->drifts()->create(['field' => $field, 'expected_value' => is_scalar($expected) ? (string) $expected : json_encode($expected, JSON_THROW_ON_ERROR), 'actual_value' => is_scalar($actual) ? (string) $actual : json_encode($actual, JSON_THROW_ON_ERROR), 'severity' => $severity, 'status' => DriftStatus::Open]);
            $run->update(['status' => SettlementStatus::Exception]);
            $run->audits()->create(['event_type' => 'provider_drift_recorded', 'payload' => ['drift_id' => $drift->id, 'field' => $field, 'severity' => $severity], 'payload_hash' => hash('sha256', $field.':'.json_encode($actual, JSON_THROW_ON_ERROR)), 'created_at' => now()]);

            return $drift->refresh();
        });
    }
}
