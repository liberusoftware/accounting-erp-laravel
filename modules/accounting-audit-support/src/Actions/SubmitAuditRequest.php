<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupport\Actions;

use Carbon\Carbon;
use Liberu\Accounting\AuditSupport\Enums\AuditRequestStatus;
use Liberu\Accounting\AuditSupport\Models\AuditRequest;

final class SubmitAuditRequest
{
    public function handle(AuditRequest $request): AuditRequest
    {
        if (! in_array($request->status, [AuditRequestStatus::Open, AuditRequestStatus::InProgress], true)) {
            throw new \InvalidArgumentException('Only open requests may be submitted.');
        }

        return tap($request)->update(['status' => AuditRequestStatus::Submitted, 'submitted_at' => Carbon::now()]);
    }
}
