# Liberu Accounting Core API

This package is the one-to-one HTTP adapter for
`liberusoftware/module-accounting-core`. It exposes only the authenticated,
ability-scoped Accounting Core operations owned by the module.

## Installation

```bash
composer require liberusoftware/module-accounting-core-api
```

The host must configure Sanctum and grant `accounting.core.read` or
`accounting.core.write` token abilities. The package does not depend on host
application classes or expose another module's data.

## Contract

Routes are registered below `/api/v1/accounting/accounting-core`. The API
exposes legal entities, books, fiscal calendars, numbering sequences (including
atomic number allocation), and book-scoped defaults and policies. The versioned
OpenAPI 3.1 fragment is stored at `openapi/v1/accounting-core.yaml`.

## Compatibility

PHP 8.5 · Laravel 13 · Sanctum 4 · MIT
