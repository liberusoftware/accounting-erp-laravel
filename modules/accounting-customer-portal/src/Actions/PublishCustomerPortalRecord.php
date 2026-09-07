<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal\Actions;

use Illuminate\Support\Carbon;
use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalStatus;
use Liberu\Accounting\CustomerPortal\Exceptions\InvalidCustomerPortalRecord;
use Liberu\Accounting\CustomerPortal\Models\CustomerPortalRecord;

final class PublishCustomerPortalRecord
{
    public function handle(CustomerPortalRecord $record): CustomerPortalRecord
    {
        if ($record->status !== CustomerPortalStatus::Draft) {
            throw new InvalidCustomerPortalRecord('Only draft records can be published.');
        } $record->update(['status' => CustomerPortalStatus::Published, 'published_at' => Carbon::now()]);

        return $record->fresh();
    }
}
