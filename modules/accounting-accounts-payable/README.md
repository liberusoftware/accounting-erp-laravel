# Accounting Accounts Payable

Owns the supplier subledger, payable open items, payments and applications,
aging, balances, disputes, payment holds, and control-account reconciliation.

Use `CreateOpenItem`, `RecordPayment`, `ApplyPayment`, `OpenDispute`,
`ResolveDispute`, and `SetPaymentControl` as the mutation boundary. Supplier
identity, currency matching, duplicate applications, dispute transitions, and
payment-control invariants are enforced in the domain actions. Domain events
are dispatched after the surrounding transaction commits.

The module consumes supplier identity from Financial Master Data and exposes
optional API, Filament 5, and Livewire 4 adapters.
