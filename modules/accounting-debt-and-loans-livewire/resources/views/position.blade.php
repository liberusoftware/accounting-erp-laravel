<div>
    <h2>Debt position</h2>
    <p>Outstanding: {{ number_format($position['outstanding'], 2) }}</p>
    <p>Due within one year: {{ number_format($position['current_due_next_year'], 2) }}</p>
</div>
