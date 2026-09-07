# Accounting Bank Accounts API

Authenticated endpoints live under `/api/v1/accounting/bank-accounts` and
require `accounting.bank-accounts.read` or `accounting.bank-accounts.write`.
Restricted account and routing numbers are never returned; resources expose
only masked values.
