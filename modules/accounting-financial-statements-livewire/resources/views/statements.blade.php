<div>
    <div class="mb-4">
        <label for="statement-type">Statement</label>
        <select id="statement-type" wire:model.live="statementType">
            @foreach ($statementTypes as $type)
                <option value="{{ $type->value }}">{{ str_replace('_', ' ', ucfirst($type->value)) }}</option>
            @endforeach
        </select>
    </div>

    <h2>{{ str_replace('_', ' ', ucfirst($statement['type'])) }}</h2>
    @if (isset($statement['net_income']))
        <p>Net income: {{ number_format($statement['net_income'], 2) }}</p>
    @endif
    @if (isset($statement['revenue']['total']))
        <p>Revenue: {{ number_format($statement['revenue']['total'], 2) }}</p>
        <p>Expenses: {{ number_format($statement['expenses']['total'], 2) }}</p>
    @endif
    @if (isset($statement['assets']['total']))
        <p>Total assets: {{ number_format($statement['assets']['total'], 2) }}</p>
        <p>Total liabilities and equity: {{ number_format($statement['total_liabilities_and_equity'], 2) }}</p>
    @endif
    @if (isset($statement['ending_cash']))
        <p>Ending cash: {{ number_format($statement['ending_cash'], 2) }}</p>
    @endif
</div>
