<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Models\Invoice;

class PortalBalanceWidget extends BaseWidget
{
    /**
     * @return array<int, Stat>
     */
    #[\Override]
    protected function getStats(): array
    {
        $customerId = Auth::guard('customer')->id();

        $outstanding = (float) Invoice::query()
            ->where('customer_id', $customerId)
            ->where('payment_status', '!=', 'paid')
            ->sum('total_amount');

        $count = Invoice::query()->where('customer_id', $customerId)->count();

        return [
            Stat::make('Outstanding balance', number_format($outstanding, 2)),
            Stat::make('Invoices', (string) $count),
        ];
    }
}
