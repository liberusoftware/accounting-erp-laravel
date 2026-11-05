# Accounting Erp

Integrated ERP workflows and the existing application accounting schema.

This package owns the existing ERP schema and workflows, alongside the newer accounting capability modules. Migration filenames are retained so existing installations do not replay migrations.

The host selects authentication models in config and composes the Filament panels. This module owns the integrated model graph, accounting services, notifications, approval events, console commands, identity actions, factories, seeders, and migrations. The schema differs from the newer `accounting-*` capability schemas, so those remain separate.

`compatibility.php` lazily aliases former application class names for persisted jobs and serialized data. `morph-map.php` retains the original polymorphic discriminators for existing approvals and documents. New code should import the package namespaces. Do not remove these compatibility maps while stored references still use them.

Migrations load through `ErpServiceProvider`. The host DatabaseSeeder delegates to this package; seeding is explicit and never runs when the provider boots.
