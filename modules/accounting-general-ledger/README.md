# Accounting General Ledger

Authoritative journal lifecycle and balanced posting boundary for Liberu accounting applications.

`CreateJournal` validates the book and account legal-entity boundary, requires
balanced debit/credit lines, and creates draft journals. `PostJournal` enforces
active/manual-entry accounts and makes posted entries immutable.
`ReverseJournal` creates and posts an opposite entry while preserving the
original audit trail. Recurring, correction, allocation, accrual, and
prepayment actions use the same public boundary.
