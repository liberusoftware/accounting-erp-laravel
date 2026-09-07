<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal\Actions;

use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalRecordType;
use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalStatus;
use Liberu\Accounting\CustomerPortal\Exceptions\InvalidCustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Models\CustomerPortalRecord;

final class ResolveCustomerPortalDispute
{
    public function handle(CustomerPortalRecord $record, array $resolution): CustomerPortalRecord
    {
        if ($record->type !== CustomerPortalRecordType::Dispute || $record->status !== CustomerPortalStatus::Disputed) {
            throw new InvalidCustomerPortalRecord('Only active disputes can be resolved.');
        } $record->update(['status' => CustomerPortalStatus::Resolved, 'metadata' => array_merge($record->metadata ?? [], ['resolution' => $resolution])]);

        return $record->fresh();
    }
}
