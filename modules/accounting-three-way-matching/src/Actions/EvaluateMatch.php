<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ThreeWayMatching\Enums\ExceptionSeverity;
use Liberu\Accounting\ThreeWayMatching\Enums\ExceptionStatus;
use Liberu\Accounting\ThreeWayMatching\Enums\MatchStatus;
use Liberu\Accounting\ThreeWayMatching\Events\MatchEvaluated;
use Liberu\Accounting\ThreeWayMatching\Exceptions\InvalidMatch;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

final class EvaluateMatch
{
    public function handle(array $attributes): MatchRecord
    {
        return DB::transaction(function () use ($attributes): MatchRecord {
            $required = ['purchase_order_type', 'purchase_order_id', 'receipt_type', 'receipt_id', 'bill_type', 'bill_id', 'currency'];
            foreach ($required as $key) {
                if (blank($attributes[$key] ?? null)) {
                    throw new InvalidMatch("Missing matching reference [{$key}].");
                }
            }
            $numbers = ['ordered_quantity', 'received_quantity', 'billed_quantity', 'ordered_unit_price', 'billed_unit_price', 'expected_tax', 'billed_tax'];
            foreach ($numbers as $key) {
                if (! isset($attributes[$key]) || (float) $attributes[$key] < 0) {
                    throw new InvalidMatch("Matching value [{$key}] must be zero or greater.");
                }
            }
            if ((float) $attributes['ordered_quantity'] <= 0 || (float) $attributes['received_quantity'] <= 0 || (float) $attributes['billed_quantity'] <= 0) {
                throw new InvalidMatch('Order, receipt, and bill quantities must be positive.');
            }
            $existing = MatchRecord::query()->where(array_intersect_key($attributes, array_flip($required)))->first();
            if ($existing) {
                return $existing->load('exceptions', 'evidence');
            }
            $quantityTolerance = max(0, (float) ($attributes['quantity_tolerance'] ?? 0));
            $priceTolerance = max(0, (float) ($attributes['price_tolerance'] ?? 0));
            $taxTolerance = max(0, (float) ($attributes['tax_tolerance'] ?? 0));
            $exceptions = [];
            $ordered = (float) $attributes['ordered_quantity'];
            $received = (float) $attributes['received_quantity'];
            $billed = (float) $attributes['billed_quantity'];
            $orderedPrice = (float) $attributes['ordered_unit_price'];
            $billedPrice = (float) $attributes['billed_unit_price'];
            $expectedTax = (float) $attributes['expected_tax'];
            $billedTax = (float) $attributes['billed_tax'];
            if ($received > $ordered + $quantityTolerance) {
                $exceptions[] = ['kind' => 'received_exceeds_ordered', 'expected_value' => $ordered, 'actual_value' => $received, 'tolerance' => $quantityTolerance, 'severity' => ExceptionSeverity::Blocking];
            }
            if ($billed > $received + $quantityTolerance) {
                $exceptions[] = ['kind' => 'billed_exceeds_received', 'expected_value' => $received, 'actual_value' => $billed, 'tolerance' => $quantityTolerance, 'severity' => ExceptionSeverity::Blocking];
            }
            if ($billed < $received - $quantityTolerance) {
                $exceptions[] = ['kind' => 'partial_quantity', 'expected_value' => $received, 'actual_value' => $billed, 'tolerance' => $quantityTolerance, 'severity' => ExceptionSeverity::Warning];
            }
            if (abs($billedPrice - $orderedPrice) > $priceTolerance) {
                $exceptions[] = ['kind' => 'price_variance', 'expected_value' => $orderedPrice, 'actual_value' => $billedPrice, 'tolerance' => $priceTolerance, 'severity' => ExceptionSeverity::Blocking];
            }
            if (abs($billedTax - $expectedTax) > $taxTolerance) {
                $exceptions[] = ['kind' => 'tax_variance', 'expected_value' => $expectedTax, 'actual_value' => $billedTax, 'tolerance' => $taxTolerance, 'severity' => ExceptionSeverity::Blocking];
            }
            $baseStatus = $billed < $received - $quantityTolerance ? MatchStatus::Partial : MatchStatus::Matched;
            $status = collect($exceptions)->contains(fn (array $exception): bool => $exception['severity'] === ExceptionSeverity::Blocking) ? MatchStatus::Exception : $baseStatus;
            $match = MatchRecord::create(array_merge($attributes, ['quantity_tolerance' => $quantityTolerance, 'price_tolerance' => $priceTolerance, 'tax_tolerance' => $taxTolerance, 'status' => $status, 'metadata' => array_merge($attributes['metadata'] ?? [], ['base_status' => $baseStatus->value])]));
            foreach ($exceptions as $exception) {
                $match->exceptions()->create(array_merge($exception, ['status' => ExceptionStatus::Open]));
            }
            $match = $match->refresh()->load('exceptions');
            DB::afterCommit(fn () => event(new MatchEvaluated($match)));

            return $match;
        });
    }
}
