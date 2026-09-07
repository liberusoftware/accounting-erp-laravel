# Accounting Accounts Receivable API

Authenticated HTTP presentation boundary for the Accounts Receivable domain module.

Routes are versioned under `/api/v1/accounting/accounts-receivable` and require
Sanctum plus `accounting.receivables.read` or `accounting.receivables.write`.
The contract includes open items, aging, statements, balances, receipts and
applications, disputes, credit control, and reconciliation. Responses use
explicit resources for open items, receipts, and disputes.
