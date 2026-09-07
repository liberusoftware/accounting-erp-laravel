# Accounting Dimensions and Tracking API

Authenticated HTTP adapter for dimensions, values, validation, allocations, and dimensional balances.

Routes are versioned under `/api/v1/accounting/dimensions-and-tracking` and
require `accounting.dimensions.read` or `accounting.dimensions.write` Sanctum
abilities. The OpenAPI contract documents the stable operations.
