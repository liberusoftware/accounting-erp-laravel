# Accounting Chart of Accounts Filament

Filament 5 presentation adapter for the chart-of-accounts module. Attach
`ChartOfAccountsFilamentPlugin` to an application panel explicitly.
Create and edit pages delegate to `SaveAccount`; the table archive action
delegates to `ArchiveAccount` so UI mutations retain domain invariants.
