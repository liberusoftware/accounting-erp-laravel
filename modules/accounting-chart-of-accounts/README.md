# Liberu Accounting Chart of Accounts

This module owns the authoritative account hierarchy for an accounting legal
entity. It validates account codes, account types, normal balances, parent
relationships, manual-entry restrictions, control-account flags, localization,
and archival lifecycle rules. Presentation adapters depend on this public
boundary and do not duplicate its domain rules.

`SaveAccount` derives and validates normal balances, rejects duplicate codes,
cross-entity or inactive parents, self-parenting, and descendant cycles.
`ArchiveAccount` prevents archiving accounts with active children and emits
after-commit lifecycle events.
