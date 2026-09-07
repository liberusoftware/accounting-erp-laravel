# Accounting Dimensions and Tracking

Owned registry and validation boundary for accounting classes, locations, departments, centers, projects, tags, allocations, and dimensional balances.

`SaveDimension` and `SaveDimensionValue` enforce scoped code uniqueness and
inactive-dimension rules. `ValidateDimensions` is the authoritative validation
boundary, and `AllocateDimensions` requires valid dimension payloads, positive
amounts, percentages totaling 100%, and an idempotency key.
