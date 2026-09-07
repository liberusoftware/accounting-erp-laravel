<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupport\Actions;

use Liberu\Accounting\AuditSupport\Enums\AuditRequestStatus;
use Liberu\Accounting\AuditSupport\Models\AuditRequest;

final class CreateAuditRequest
{
    public function handle(array $attributes): AuditRequest
    {
        foreach (['team_id', 'title'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new \InvalidArgumentException("{$field} is required.");
            }
        }

        return AuditRequest::create([...$attributes, 'status' => AuditRequestStatus::Open, 'reference' => $attributes['reference'] ?? 'AUD-'.strtoupper(str()->random(8))]);
    }
}
