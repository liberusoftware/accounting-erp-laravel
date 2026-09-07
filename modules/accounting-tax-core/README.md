# Accounting Tax Core

Owns tax codes and rates, inclusive/exclusive/zero-rated treatment, jurisdiction and effective-date selection, exemptions, rounding, control-account references, and immutable source evidence.

Use `CreateTaxRule`, `UpdateTaxRule`, `ActivateTaxRule`, `ArchiveTaxRule`, `CalculateTax`, `CaptureTaxEvidence`, and `ActiveTaxRuleQuery` as the public application boundary. Tax rules use opaque jurisdiction and account identifiers so this package does not couple to an application's models.
