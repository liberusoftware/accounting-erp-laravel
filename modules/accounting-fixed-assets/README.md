# Accounting fixed assets

The `accounting-fixed-assets` package owns the tenant-scoped fixed-asset register. It provides categories, acquisition, capitalization, components, locations, custodians, asset books, supporting documents, disposal, archival, public queries, and post-commit domain events.

## Installation

Install the domain package and any presentation package required by the host application:

```bash
composer require liberusoftware/module-accounting-fixed-assets
```

The package loads its migration automatically. The host must provide an authenticated team context for user-facing operations; the domain actions also accept an explicit `team_id` for trusted application workflows.

## Public boundary

Mutations are exposed as one action per use case:

- `CreateCategory`, `AcquireAsset`, `UpdateAsset`, `CapitalizeAsset`, `DisposeAsset`, and `ArchiveAsset`;
- `AddAssetComponent`, `AddAssetDocument`, `CreateLocation`, and `CreateCustodian`;
- `AssignAssetLocation` and `AssignAssetCustodian`.

`AssetQuery` provides tenant/status-filtered pagination and register summaries. Actions enforce lifecycle invariants, duplicate references, team ownership, positive money/useful-life values, and closed-asset restrictions. Presentation packages must call these actions and must not write the owned tables directly.

The package emits `AssetAcquired`, `AssetCapitalized`, `AssetComponentAdded`, `AssetDocumentAdded`, and `AssetDisposed` after the surrounding database transaction commits. Event payloads contain the affected public model and action-specific reference where applicable.

## Data ownership and safety

All tables are prefixed `accounting_fixed_asset_` and belong exclusively to this package. Money is stored with two decimal places and every asset has an explicit ISO currency code. Location and custodian assignments are tenant checked. API consumers receive explicit resource attributes and money objects; Eloquent models are never serialized automatically.

Invalid transitions throw `InvalidAsset`. Authorization is registered through `AssetPolicy` and requires the authenticated user’s current team to match the asset tenant. API callers additionally receive concealment-safe responses for assets outside their current team.

## Presentation packages

- `module-accounting-fixed-assets-api` publishes `/api/v1/accounting/fixed-assets` and the versioned OpenAPI fragment under `openapi/v1/`.
- `module-accounting-fixed-assets-filament` provides the opt-in Filament 5 resource/plugin.
- `module-accounting-fixed-assets-livewire` provides the opt-in Livewire 4 asset register component alias `module-accounting-fixed-assets::assets`.

## Verification

From the host repository, run:

```bash
php artisan module:validate
php artisan test tests/Feature/AccountingFixedAssetsTest.php --compact
php -d memory_limit=512M vendor/bin/phpstan analyse modules/accounting-fixed-assets modules/accounting-fixed-assets-api modules/accounting-fixed-assets-filament modules/accounting-fixed-assets-livewire --no-progress
```

The test suite covers the lifecycle, post-commit events, duplicate and invalid transitions, tenant-owned location/custodian records, API authentication, and tenant isolation.
