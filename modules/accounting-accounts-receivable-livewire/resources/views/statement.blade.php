<div>
    <h2>Customer statement</h2>
    <p>Opening balance: {{ number_format($statement['opening_balance'], 2) }}</p>
    <p>Charges: {{ number_format($statement['charges'], 2) }}</p>
    <p>Payments: {{ number_format($statement['payments'], 2) }}</p>
    <p>Closing balance: {{ number_format($statement['closing_balance'], 2) }}</p>
</div>
