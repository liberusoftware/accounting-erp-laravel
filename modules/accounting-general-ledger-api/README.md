# Accounting General Ledger API

Authenticated API adapter for journals, posting actions, reversals, and account balances.

Routes are versioned under `/api/v1/accounting/general-ledger` and require
Sanctum abilities `accounting.general-ledger.read` or
`accounting.general-ledger.write`. The OpenAPI contract covers balanced
journals, typed journals, recurring generation, posting, reversal, and
balances.
