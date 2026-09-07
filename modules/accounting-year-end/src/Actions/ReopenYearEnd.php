<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Actions;

use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndClose;

final class ReopenYearEnd
{
    public function handle(YearEndClose $close): YearEndClose
    {
        if ($close->status === YearEndStatus::Locked) {
            throw new InvalidYearEnd('Locked year ends require an authorized administrative override.');
        }
        if ($close->status !== YearEndStatus::Closed) {
            throw new InvalidYearEnd('Only closed year ends can be reopened.');
        }
        $close->update(['status' => YearEndStatus::Reopened]);

        return $close->refresh();
    }
}
