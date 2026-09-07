# Accounting Wise

Wise OAuth, profile, balance, and transfer adapter. Transfer payloads are normalized into the provider-neutral Bank Feeds contract, while single and bounded bulk payments implement the shared `PaymentProviderAdapter`; credentials and provider HTTP behavior stay inside this adapter.

Wise does not expose a provider-side bulk-transfer endpoint for this workflow. Bulk submissions are therefore bounded to 100 items and sent as independent authenticated transfers, allowing callers to retry or reconcile each returned provider result.
