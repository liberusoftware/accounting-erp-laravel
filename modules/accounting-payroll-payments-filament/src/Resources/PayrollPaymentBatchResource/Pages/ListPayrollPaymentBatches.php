<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsFilament\Resources\PayrollPaymentBatchResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\PayrollPaymentsFilament\Resources\PayrollPaymentBatchResource;

final class ListPayrollPaymentBatches extends ListRecords
{
    protected static string $resource = PayrollPaymentBatchResource::class;
}
