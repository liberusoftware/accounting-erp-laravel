<div>
    <h2>Accounts Payable reconciliation</h2>
    <p>Subledger balance: {{ number_format($reconciliation['subledger_balance'], 2) }}</p>
    @if ($reconciliation['difference'] !== null)
        <p>Difference: {{ number_format($reconciliation['difference'], 2) }}</p>
    @endif
</div>
