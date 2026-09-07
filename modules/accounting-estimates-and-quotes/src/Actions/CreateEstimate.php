<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Exceptions\InvalidEstimate;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final class CreateEstimate
{
    public function handle(array $a): Estimate
    {
        foreach (['legal_entity_id', 'customer_ref', 'quote_ref', 'name', 'currency', 'issue_date'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidEstimate("Missing estimate field [{$k}].");
            }
        }if (! preg_match('/^[A-Za-z]{3}$/', (string) $a['currency']) || ($a['expires_on'] ?? null) && $a['expires_on'] < $a['issue_date']) {
            throw new InvalidEstimate('Estimate currency or dates are invalid.');
        }

        return DB::transaction(function () use ($a): Estimate {
            $e = Estimate::create(['legal_entity_id' => $a['legal_entity_id'], 'customer_ref' => $a['customer_ref'], 'quote_ref' => $a['quote_ref'], 'name' => $a['name'], 'currency' => strtoupper($a['currency']), 'status' => EstimateStatus::Draft, 'issue_date' => $a['issue_date'], 'expires_on' => $a['expires_on'] ?? null, 'terms' => $a['terms'] ?? null, 'brand' => $a['brand'] ?? null, 'metadata' => $a['metadata'] ?? null]);
            $e->versions()->create(['version' => 1, 'snapshot' => $e->load('items')->toArray(), 'created_by' => $a['created_by'] ?? null]);
            $e->history()->create(['event' => 'created', 'actor_ref' => $a['created_by'] ?? null]);

            return $e->refresh();
        });
    }
}
