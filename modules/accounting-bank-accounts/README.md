# Accounting Bank Accounts

Owns bank, current, savings, credit, loan, and cash account records,
including currency, opening data, lifecycle state, feed references, and
encrypted restricted details. Mutations use `CreateBankAccount`,
`UpdateBankAccount`, and `SetBankAccountStatus`; presentation adapters are
optional and must delegate to these domain boundaries.
