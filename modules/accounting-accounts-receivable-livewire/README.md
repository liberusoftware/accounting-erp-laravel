# Accounting Accounts Receivable Livewire

Optional Livewire customer-balance and open-item boundary.

The bounded component namespace exposes:

- `module-accounting-accounts-receivable::receivables` for the customer subledger;
- `module-accounting-accounts-receivable::aging` for aging buckets; and
- `module-accounting-accounts-receivable::statement` for a customer statement.

The package also keeps kebab-case compatibility aliases for hosts that do not
support namespaced component lookup. Components require an authenticated user,
validate their public state, and delegate reads to the domain query objects.
