# Accounting Chart of Accounts API

Authenticated API presentation adapter for `module-accounting-chart-of-accounts`.
Routes are versioned below `/api/v1/accounting/chart-of-accounts` and require
the `accounting.chart.read` or `accounting.chart.write` Sanctum ability.
The API supports filtered account lists, hierarchy trees, CRUD/archive
operations, and explicit JSON:API-style account resources.
