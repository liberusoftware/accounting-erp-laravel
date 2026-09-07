# Accounting Accounts Receivable

Owns customer receivable accounts, open items, receipts and applications, aging,
statements, disputes, credit control, and control-account reconciliation.

## Public domain boundary

Use `CreateOpenItem`, `RecordReceipt`, `ApplyReceipt`, `OpenDispute`,
`ResolveDispute`, `SetCreditControl`, and `SyncFinalizedInvoice` for mutations.
Queries are exposed through `CustomerSubledgerQuery`, `StatementQuery`,
`AgingQuery`, and `ControlAccountReconciliationQuery`. Mutations validate that
referenced parties are customers, prevent duplicate applications, and dispatch
past-tense events after a successful transaction commit.

The module depends only on the shared core and financial master-data contracts.
Presentation adapters are optional and do not own business rules.

## Operational notes

Run the package migrations before enabling the API or UI adapters. API clients
need `accounting.receivables.read` and/or `accounting.receivables.write`
Sanctum abilities. The API intentionally serializes receipt and dispute data
through explicit resources and never exposes internal model state implicitly.

Independent customer subledger, open-item, receipt, statement, aging, dispute, credit-control, and control-reconciliation boundary.
