# Accounting Accounts Payable API

Authenticated HTTP boundary under `/api/v1/accounting/accounts-payable`.
Routes require Sanctum and `accounting.payables.read` or
`accounting.payables.write`. The API covers open items, aging, supplier
balances, payments and applications, disputes, payment control, and
reconciliation. Responses use explicit open-item, payment, and dispute
resources.
