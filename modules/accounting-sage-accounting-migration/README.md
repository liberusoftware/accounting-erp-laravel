# Accounting Sage Accounting Migration

This provider-neutral module owns Sage Accounting migration runs and opaque source records for chart and analysis types, contacts, products, sales and purchases, payments, bank, VAT/CIS evidence, currencies, budgets, attachments, and source IDs. Credentials are encrypted and never included in migration payloads. Imports are transactional and idempotent per run, entity type, and source ID; failed records remain operator-visible for recovery and reconciliation.
