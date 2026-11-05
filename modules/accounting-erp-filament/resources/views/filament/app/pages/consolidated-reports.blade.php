<x-filament-panels::page>
    {{ $this->content }}

    @if ($profitAndLoss)
        <x-filament::section heading="Profit & Loss (consolidated)">
            <div class="grid grid-cols-2 gap-2">
                <span>Revenue</span><span>{{ number_format($profitAndLoss['consolidated']['revenue'], 2) }}</span>
                <span>Expenses</span><span>{{ number_format($profitAndLoss['consolidated']['expenses'], 2) }}</span>
                <span>Intercompany eliminated (revenue)</span><span>{{ number_format($profitAndLoss['eliminations']['revenue'], 2) }}</span>
                <span class="font-bold">Net income</span><span class="font-bold">{{ number_format($profitAndLoss['consolidated']['net_income'], 2) }}</span>
            </div>
        </x-filament::section>
    @endif

    @if ($balanceSheet)
        <x-filament::section heading="Balance Sheet (consolidated)">
            <div class="grid grid-cols-2 gap-2">
                <span>Assets</span><span>{{ number_format($balanceSheet['consolidated']['assets'], 2) }}</span>
                <span>Liabilities</span><span>{{ number_format($balanceSheet['consolidated']['liabilities'], 2) }}</span>
                <span>Equity</span><span>{{ number_format($balanceSheet['consolidated']['equity'], 2) }}</span>
            </div>
        </x-filament::section>
    @endif

    @if ($cashFlow)
        <x-filament::section heading="Cash Flow (consolidated, simple sum)">
            <div class="grid grid-cols-2 gap-2">
                <span>Net change in cash</span><span>{{ number_format($cashFlow['consolidated']['net_change_in_cash'], 2) }}</span>
                <span>Ending cash</span><span>{{ number_format($cashFlow['consolidated']['ending_cash'], 2) }}</span>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
