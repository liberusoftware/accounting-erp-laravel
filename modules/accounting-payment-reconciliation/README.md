# Accounting Payment Reconciliation

Owns provider-neutral gateway and merchant settlement imports, gross-to-net fees/refunds/disputes, matching, missing-item detection, provider-drift evidence, and recovery. Provider references and source payloads remain opaque and are never coupled to an SDK or application model.

Imports are idempotent per team, provider, and settlement reference. Settlement items retain their source hash and provenance; mutations are transactional and emit past-tense events after commit. The optional API, Filament, and Livewire packages are one-to-one adapters over this public boundary.
