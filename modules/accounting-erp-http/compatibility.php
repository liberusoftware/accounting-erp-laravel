<?php

// Preserve queued jobs and serialized references created before the extraction.
$aliases = [
    'App\\Http\\Controllers\\Controller' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Controller',
    'App\\Http\\Controllers\\LoginController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\LoginController',
    'App\\Http\\Controllers\\PortalAccessController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\PortalAccessController',
    'App\\Http\\Controllers\\SaasPremiumBillingController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\SaasPremiumBillingController',
    'App\\Http\\Resources\\TransactionResource' => 'Liberu\\Accounting\\ErpHttp\\Http\\Resources\\TransactionResource',
    'App\\Http\\Controllers\\Api\\BillController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\BillController',
    'App\\Http\\Controllers\\Api\\ChartOfAccountController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\ChartOfAccountController',
    'App\\Http\\Controllers\\Api\\EstimateController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\EstimateController',
    'App\\Http\\Controllers\\Api\\GeneralLedgerController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\GeneralLedgerController',
    'App\\Http\\Controllers\\Api\\InvoiceController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\InvoiceController',
    'App\\Http\\Controllers\\Api\\JournalEntryController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\JournalEntryController',
    'App\\Http\\Controllers\\Api\\OpenApiController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\OpenApiController',
    'App\\Http\\Controllers\\Api\\PlaidController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\PlaidController',
    'App\\Http\\Controllers\\Api\\PlaidWebhookController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\PlaidWebhookController',
    'App\\Http\\Controllers\\Api\\QboController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\QboController',
    'App\\Http\\Controllers\\Api\\QboWebhookController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\QboWebhookController',
    'App\\Http\\Controllers\\Api\\RevolutController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\RevolutController',
    'App\\Http\\Controllers\\Api\\RevolutWebhookController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\RevolutWebhookController',
    'App\\Http\\Controllers\\Api\\SageController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\SageController',
    'App\\Http\\Controllers\\Api\\TransactionController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\TransactionController',
    'App\\Http\\Controllers\\Api\\WiseController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\WiseController',
    'App\\Http\\Controllers\\Api\\WiseWebhookController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\WiseWebhookController',
    'App\\Http\\Controllers\\Api\\XeroController' => 'Liberu\\Accounting\\ErpHttp\\Http\\Controllers\\Api\\XeroController',
];

spl_autoload_register(static function (string $class) use ($aliases): void {
    if (isset($aliases[$class])) {
        class_alias($aliases[$class], $class);
    }
});
