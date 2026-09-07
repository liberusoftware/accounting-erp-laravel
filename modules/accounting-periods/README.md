# Liberu Accounting Periods

Provider-neutral accounting-period lifecycle rules for open, soft-closed, and
hard-closed periods. The module owns date-range validation, posting windows,
reopen evidence, row-locked transitions, and state-change events.

The matching API, Filament, and Livewire adapters are separate optional
packages. Period mutations must go through the domain actions so presentation
layers cannot bypass lifecycle rules.
