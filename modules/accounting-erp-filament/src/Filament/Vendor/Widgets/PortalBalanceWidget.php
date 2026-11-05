<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Vendor\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Models\Bill;

class PortalBalanceWidget extends BaseWidget
{
    /**
     * @return array<int, Stat>
     */
    #[\Override]
    protected function getStats(): array
    {
        $vendorId = Auth::guard('vendor')->id();

        $outstanding = (float) Bill::query()
            ->where('vendor_id', $vendorId)
            ->where('payment_status', '!=', 'paid')
            ->sum('total_amount');

        $count = Bill::query()->where('vendor_id', $vendorId)->count();

        return [
            Stat::make('Outstanding balance', number_format($outstanding, 2)),
            Stat::make('Bills', (string) $count),
        ];
    }
}
