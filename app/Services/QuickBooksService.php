<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Bill;
use App\Models\CreditMemo;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\QboConnection;
use App\Models\Vendor;
use App\Services\Concerns\RequestsProviderTokens;
use App\Services\Concerns\ResolvesSyncedContacts;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Two-way sync with QuickBooks Online via OAuth 2.0.
 *
 * Mirrors the PlaidService pattern: hand-rolled HTTP through the Laravel client,
 * config under services.qbo, encrypted tokens on QboConnection.
 *
 * Invoices, accounts, bills and payments all round-trip both directions.
 */
class QuickBooksService
{
    use RequestsProviderTokens;
    use ResolvesSyncedContacts;

    /** @var array<string, mixed> */
    private array $cfg;

    public function __construct()
    {
        $this->cfg = config('services.qbo');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return $this->cfg['authorization_url'].'?'.http_build_query([
            'client_id' => $this->cfg['client_id'],
            'response_type' => 'code',
            'scope' => 'com.intuit.quickbooks.accounting',
            'redirect_uri' => $this->cfg['redirect_uri'],
            'state' => $state,
        ]);
    }

    /**
     * Exchange an authorization code for tokens and persist the connection.
     */
    public function handleCallback(int $userId, string $code, string $realmId): QboConnection
    {
        $tokens = $this->requestTokens([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->cfg['redirect_uri'],
        ]);

        return QboConnection::updateOrCreate(
            ['user_id' => $userId, 'realm_id' => $realmId],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'token_expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 3600)),
                'status' => 'active',
            ],
        );
    }

    /**
     * Push a local invoice to QBO. Creates if unmapped, sparse-updates if already synced.
     */
    public function pushInvoice(Invoice $invoice, QboConnection $connection): Invoice
    {
        $payload = [
            'Line' => [[
                'Amount' => (float) $invoice->total_amount,
                'DetailType' => 'SalesItemLineDetail',
                'SalesItemLineDetail' => ['ItemRef' => ['value' => '1']],
            ]],
            'CustomerRef' => ['value' => (string) (Customer::whereKey($invoice->customer_id)->value('qbo_id') ?? '1')],
        ];

        if ($invoice->qbo_id) {
            $payload['Id'] = $invoice->qbo_id;
            $payload['SyncToken'] = $invoice->qbo_sync_token ?? '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/invoice', $payload)->throw()->json();

        $invoice->update([
            'qbo_id' => $body['Invoice']['Id'] ?? $invoice->qbo_id,
            'qbo_sync_token' => $body['Invoice']['SyncToken'] ?? $invoice->qbo_sync_token,
        ]);

        return $invoice;
    }

    /**
     * Pull invoices from QBO into local records. Returns the number processed.
     */
    public function pullInvoices(QboConnection $connection): int
    {
        $body = $this->client($connection)
            ->get('/query', ['query' => 'select * from Invoice'])
            ->throw()
            ->json();

        $rows = $body['QueryResponse']['Invoice'] ?? [];

        foreach ($rows as $row) {
            Invoice::updateOrCreate(
                ['qbo_id' => $row['Id'], 'team_id' => $connection->team_id],
                [
                    'invoice_number' => $row['DocNumber'] ?? ('QBO-'.$row['Id']),
                    'customer_id' => $this->resolveCustomerId($row['CustomerRef'] ?? null, $connection->team_id),
                    'total_amount' => $row['TotalAmt'] ?? 0,
                    'invoice_date' => $row['TxnDate'] ?? now()->toDateString(),
                    'qbo_sync_token' => $row['SyncToken'] ?? null,
                    'payment_status' => 'pending',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    /**
     * Pull every accounting entity currently supported by the QBO adapter.
     *
     * @return array{customers:int,vendors:int,accounts:int,invoices:int,bills:int,payments:int,estimates:int,credit_memos:int,transactions:int}
     */
    public function sync(QboConnection $connection): array
    {
        return [
            'customers' => $this->pullCustomers($connection),
            'vendors' => $this->pullVendors($connection),
            'accounts' => $this->pullAccounts($connection),
            'invoices' => $this->pullInvoices($connection),
            'bills' => $this->pullBills($connection),
            'payments' => $this->pullPayments($connection),
            'estimates' => $this->pullEstimates($connection),
            'credit_memos' => $this->pullCreditMemos($connection),
            'transactions' => $this->pullTransactions($connection),
        ];
    }

    public function pullTransactions(QboConnection $connection): int
    {
        $rows = [];
        foreach (['Purchase', 'Deposit', 'Transfer'] as $entity) {
            foreach ($this->query($connection, 'select * from '.$entity)[$entity] ?? [] as $row) {
                $row['_entity'] = $entity;
                $rows[] = $row;
            }
        }

        return app(ProviderTransactionImporter::class)->handle($rows, $connection->team_id, function (array $row) use ($connection): array {
            $entity = (string) $row['_entity'];
            $amount = (float) ($row['TotalAmt'] ?? $row['TransferAmt'] ?? 0);
            if ($entity === 'Purchase') {
                $amount *= -1;
            }

            $description = (string) ($row['PrivateNote'] ?? $row['Memo'] ?? 'QuickBooks '.$entity);

            return [
                'external_id' => 'qbo:'.$entity.':'.(string) $row['Id'],
                'date' => (string) ($row['TxnDate'] ?? now()->toDateString()),
                'amount' => $amount,
                'description' => $description,
                'account_id' => app(ProviderTransactionImporter::class)->accountId('qbo_id', $row['AccountRef']['value'] ?? null, $connection->team_id),
                'type' => strtolower($entity),
            ];
        });
    }

    public function pushEstimate(Estimate $estimate, QboConnection $connection): Estimate
    {
        $payload = [
            'CustomerRef' => ['value' => (string) (Customer::whereKey($estimate->customer_id)->value('qbo_id') ?? '1')],
            'TxnDate' => optional($estimate->estimate_date)->format('Y-m-d') ?? (string) $estimate->estimate_date,
            'ExpirationDate' => optional($estimate->expiration_date)->format('Y-m-d'),
            'DocNumber' => $estimate->estimate_number,
            'Line' => [[
                'Amount' => (float) $estimate->total_amount,
                'DetailType' => 'SalesItemLineDetail',
                'SalesItemLineDetail' => ['ItemRef' => ['value' => '1']],
            ]],
        ];

        if ($estimate->qbo_id) {
            $payload['Id'] = $estimate->qbo_id;
            $payload['SyncToken'] = '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/estimate', $payload)->throw()->json();
        $estimate->update(['qbo_id' => $body['Estimate']['Id'] ?? $estimate->qbo_id]);

        return $estimate;
    }

    public function pullEstimates(QboConnection $connection): int
    {
        $rows = $this->query($connection, 'select * from Estimate')['Estimate'] ?? [];

        foreach ($rows as $row) {
            if (! isset($row['Id'])) {
                continue;
            }

            Estimate::updateOrCreate(
                ['qbo_id' => (string) $row['Id'], 'team_id' => $connection->team_id],
                [
                    'customer_id' => $this->resolveCustomerId($row['CustomerRef'] ?? null, $connection->team_id),
                    'estimate_number' => $row['DocNumber'] ?? 'QBO-'.$row['Id'],
                    'estimate_date' => $row['TxnDate'] ?? now()->toDateString(),
                    'expiration_date' => $row['ExpirationDate'] ?? null,
                    'total_amount' => $row['TotalAmt'] ?? 0,
                    'subtotal_amount' => $row['TotalAmt'] ?? 0,
                    'status' => strtolower((string) ($row['CustomerMemo']['value'] ?? 'draft')),
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushCreditMemo(CreditMemo $creditMemo, QboConnection $connection): CreditMemo
    {
        $payload = [
            'CustomerRef' => ['value' => (string) (Customer::whereKey($creditMemo->customer_id)->value('qbo_id') ?? '1')],
            'TxnDate' => optional($creditMemo->credit_memo_date)->format('Y-m-d') ?? (string) $creditMemo->credit_memo_date,
            'DocNumber' => $creditMemo->credit_memo_number,
            'Line' => [[
                'Amount' => (float) $creditMemo->total_amount,
                'DetailType' => 'SalesItemLineDetail',
                'SalesItemLineDetail' => ['ItemRef' => ['value' => '1']],
            ]],
        ];

        if ($creditMemo->qbo_id) {
            $payload['Id'] = $creditMemo->qbo_id;
            $payload['SyncToken'] = '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/creditmemo', $payload)->throw()->json();
        $creditMemo->update(['qbo_id' => $body['CreditMemo']['Id'] ?? $creditMemo->qbo_id]);

        return $creditMemo;
    }

    public function pullCreditMemos(QboConnection $connection): int
    {
        $rows = $this->query($connection, 'select * from CreditMemo')['CreditMemo'] ?? [];

        foreach ($rows as $row) {
            if (! isset($row['Id'])) {
                continue;
            }

            CreditMemo::updateOrCreate(
                ['qbo_id' => (string) $row['Id'], 'team_id' => $connection->team_id],
                [
                    'customer_id' => $this->resolveCustomerId($row['CustomerRef'] ?? null, $connection->team_id),
                    'credit_memo_number' => $row['DocNumber'] ?? 'QBO-'.$row['Id'],
                    'credit_memo_date' => $row['TxnDate'] ?? now()->toDateString(),
                    'total_amount' => $row['TotalAmt'] ?? 0,
                    'subtotal_amount' => $row['TotalAmt'] ?? 0,
                    'status' => 'open',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushCustomer(Customer $customer, QboConnection $connection): Customer
    {
        $payload = [
            'DisplayName' => trim($customer->customer_name.' '.$customer->customer_last_name),
            'PrimaryEmailAddr' => ['Address' => $customer->customer_email],
            'PrimaryPhone' => ['FreeFormNumber' => $customer->customer_phone],
        ];

        if ($customer->qbo_id) {
            $payload['Id'] = $customer->qbo_id;
            $payload['SyncToken'] = '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/customer', $payload)->throw()->json();
        $customer->update(['qbo_id' => $body['Customer']['Id'] ?? $customer->qbo_id]);

        return $customer;
    }

    public function pullCustomers(QboConnection $connection): int
    {
        $rows = $this->query($connection, 'select * from Customer')['Customer'] ?? [];

        foreach ($rows as $row) {
            $id = $row['Id'] ?? null;
            if ($id === null) {
                continue;
            }

            Customer::updateOrCreate(
                ['qbo_id' => (string) $id, 'team_id' => $connection->team_id],
                [
                    'customer_name' => $row['DisplayName'] ?? 'QBO Customer '.$id,
                    'customer_last_name' => '',
                    'customer_email' => $row['PrimaryEmailAddr']['Address'] ?? 'qbo-'.$id.'@imported.invalid',
                    'customer_phone' => $row['PrimaryPhone']['FreeFormNumber'] ?? 'qbo-'.$id,
                    'customer_address' => $row['BillAddr']['Line1'] ?? 'Imported from QuickBooks Online',
                    'customer_city' => $row['BillAddr']['City'] ?? 'Unknown',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushVendor(Vendor $vendor, QboConnection $connection): Vendor
    {
        $payload = [
            'DisplayName' => $vendor->name,
            'PrimaryEmailAddr' => ['Address' => $vendor->email],
            'PrimaryPhone' => ['FreeFormNumber' => $vendor->phone],
        ];

        if ($vendor->qbo_id) {
            $payload['Id'] = $vendor->qbo_id;
            $payload['SyncToken'] = '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/vendor', $payload)->throw()->json();
        $vendor->update(['qbo_id' => $body['Vendor']['Id'] ?? $vendor->qbo_id]);

        return $vendor;
    }

    public function pullVendors(QboConnection $connection): int
    {
        $rows = $this->query($connection, 'select * from Vendor')['Vendor'] ?? [];

        foreach ($rows as $row) {
            $id = $row['Id'] ?? null;
            if ($id === null) {
                continue;
            }

            Vendor::updateOrCreate(
                ['qbo_id' => (string) $id, 'team_id' => $connection->team_id],
                [
                    'name' => $row['DisplayName'] ?? 'QBO Vendor '.$id,
                    'email' => $row['PrimaryEmailAddr']['Address'] ?? 'qbo-vendor-'.$id.'@imported.invalid',
                    'phone' => $row['PrimaryPhone']['FreeFormNumber'] ?? null,
                    'address' => $row['BillAddr']['Line1'] ?? 'Imported from QuickBooks Online',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    /** QBO Account.Classification → local account_type. */
    private const QBO_CLASSIFICATION = [
        'Asset' => 'asset',
        'Liability' => 'liability',
        'Equity' => 'equity',
        'Revenue' => 'revenue',
        'Expense' => 'expense',
    ];

    /** local account_type → QBO AccountType. */
    private const QBO_ACCOUNT_TYPE = [
        'asset' => 'Other Current Asset',
        'liability' => 'Other Current Liability',
        'equity' => 'Equity',
        'revenue' => 'Income',
        'income' => 'Income',
        'expense' => 'Expense',
    ];

    public function pushAccount(Account $account, QboConnection $connection): Account
    {
        $payload = [
            'Name' => $account->account_name,
            'AccountType' => self::QBO_ACCOUNT_TYPE[$account->account_type] ?? 'Other Current Asset',
        ];

        if ($account->qbo_id) {
            $payload['Id'] = $account->qbo_id;
            $payload['SyncToken'] = $account->qbo_sync_token ?? '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/account', $payload)->throw()->json();

        $account->update([
            'qbo_id' => $body['Account']['Id'] ?? $account->qbo_id,
            'qbo_sync_token' => $body['Account']['SyncToken'] ?? $account->qbo_sync_token,
        ]);

        return $account;
    }

    public function pullAccounts(QboConnection $connection): int
    {
        $rows = $this->query($connection, 'select * from Account')['Account'] ?? [];

        foreach ($rows as $row) {
            Account::updateOrCreate(
                ['qbo_id' => $row['Id'], 'team_id' => $connection->team_id],
                [
                    'account_name' => $row['Name'] ?? ('QBO Account '.$row['Id']),
                    'account_type' => self::QBO_CLASSIFICATION[$row['Classification'] ?? ''] ?? 'asset',
                    'account_number' => isset($row['AcctNum']) && is_numeric($row['AcctNum'])
                        ? (int) $row['AcctNum']
                        : 9000 + (int) $row['Id'],
                    'qbo_sync_token' => $row['SyncToken'] ?? null,
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    public function pushBill(Bill $bill, QboConnection $connection): Bill
    {
        $payload = [
            'VendorRef' => ['value' => (string) (Vendor::whereKey($bill->vendor_id)->value('qbo_id') ?? '1')],
            'Line' => [[
                'Amount' => (float) $bill->total_amount,
                'DetailType' => 'AccountBasedExpenseLineDetail',
                'AccountBasedExpenseLineDetail' => [
                    'AccountRef' => ['value' => (string) (Account::whereKey($bill->items->first()->account_id ?? null)->value('qbo_id') ?? '1')],
                ],
            ]],
        ];

        if ($bill->qbo_id) {
            $payload['Id'] = $bill->qbo_id;
            $payload['SyncToken'] = $bill->qbo_sync_token ?? '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/bill', $payload)->throw()->json();

        $bill->update([
            'qbo_id' => $body['Bill']['Id'] ?? $bill->qbo_id,
            'qbo_sync_token' => $body['Bill']['SyncToken'] ?? $bill->qbo_sync_token,
        ]);

        return $bill;
    }

    public function pullBills(QboConnection $connection): int
    {
        $rows = $this->query($connection, 'select * from Bill')['Bill'] ?? [];

        foreach ($rows as $row) {
            Bill::updateOrCreate(
                ['qbo_id' => $row['Id'], 'team_id' => $connection->team_id],
                [
                    'vendor_id' => $this->resolveVendorId($row['VendorRef'] ?? null, $connection->team_id),
                    'total_amount' => $row['TotalAmt'] ?? 0,
                    'bill_date' => $row['TxnDate'] ?? now()->toDateString(),
                    'due_date' => $row['DueDate'] ?? ($row['TxnDate'] ?? now()->toDateString()),
                    'qbo_sync_token' => $row['SyncToken'] ?? null,
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    public function pushPayment(Payment $payment, QboConnection $connection): Payment
    {
        $invoice = Invoice::find($payment->invoice_id);

        $payload = [
            'TotalAmt' => (float) $payment->payment_amount,
            'CustomerRef' => ['value' => (string) ($invoice === null ? '1' : (Customer::whereKey($invoice->customer_id)->value('qbo_id') ?? '1'))],
        ];

        if ($invoice?->qbo_id) {
            $payload['Line'] = [[
                'Amount' => (float) $payment->payment_amount,
                'LinkedTxn' => [['TxnId' => $invoice->qbo_id, 'TxnType' => 'Invoice']],
            ]];
        }

        if ($payment->qbo_id) {
            $payload['Id'] = $payment->qbo_id;
            $payload['SyncToken'] = $payment->qbo_sync_token ?? '0';
            $payload['sparse'] = true;
        }

        $body = $this->client($connection)->post('/payment', $payload)->throw()->json();

        $payment->update([
            'qbo_id' => $body['Payment']['Id'] ?? $payment->qbo_id,
            'qbo_sync_token' => $body['Payment']['SyncToken'] ?? $payment->qbo_sync_token,
        ]);

        return $payment;
    }

    public function pullPayments(QboConnection $connection): int
    {
        $rows = $this->query($connection, 'select * from Payment')['Payment'] ?? [];

        foreach ($rows as $row) {
            $invoiceId = $this->resolvePaymentInvoiceId($row, $connection->team_id);
            if ($invoiceId === null) {
                continue;
            }

            Payment::updateOrCreate(
                ['qbo_id' => $row['Id'], 'team_id' => $connection->team_id],
                [
                    'invoice_id' => $invoiceId,
                    'payment_amount' => $row['TotalAmt'] ?? 0,
                    'payment_date' => $row['TxnDate'] ?? now()->toDateString(),
                    'qbo_sync_token' => $row['SyncToken'] ?? null,
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    /**
     * Map a QBO payment back to a local invoice via its LinkedTxn, by qbo_id.
     *
     * @param  array<string, mixed>  $row
     */
    private function resolvePaymentInvoiceId(array $row, ?int $teamId): ?int
    {
        $txnId = $row['Line'][0]['LinkedTxn'][0]['TxnId'] ?? null;

        if ((! is_string($txnId) && ! is_int($txnId)) || (string) $txnId === '') {
            return null;
        }
        $invoiceId = Invoice::where('qbo_id', $txnId)->where('team_id', $teamId)->value('id');

        return $invoiceId === null ? null : (int) $invoiceId;
    }

    /**
     * Run a QBO query and return the QueryResponse payload.
     *
     * @return array<string, mixed>
     */
    private function query(QboConnection $connection, string $query): array
    {
        return $this->client($connection)
            ->get('/query', ['query' => $query])
            ->throw()
            ->json()['QueryResponse'] ?? [];
    }

    /**
     * Map a QBO VendorRef onto a local vendor, creating one if unseen.
     *
     * @param  array<string, mixed>|null  $vendorRef
     */
    private function resolveVendorId(?array $vendorRef, ?int $teamId = null): int
    {
        $externalId = $vendorRef['value'] ?? null;
        if ($externalId !== null && ($localId = Vendor::where('qbo_id', (string) $externalId)->where('team_id', $teamId)->value('vendor_id')) !== null) {
            return (int) $localId;
        }

        return $this->syncedVendorId($vendorRef['name'] ?? ('QBO Vendor '.($externalId ?? 'Unknown')), 'qbo', $teamId);
    }

    /**
     * Map a QBO CustomerRef onto a local customer, creating one if unseen.
     * The invoices table requires customer_id, so pulled invoices need a customer.
     *
     * @param  array<string, mixed>|null  $customerRef
     */
    private function resolveCustomerId(?array $customerRef, ?int $teamId = null): int
    {
        $externalId = $customerRef['value'] ?? null;
        if ($externalId !== null && ($localId = Customer::where('qbo_id', (string) $externalId)->where('team_id', $teamId)->value('id')) !== null) {
            return (int) $localId;
        }

        return $this->syncedCustomerId($customerRef['name'] ?? ('QBO Customer '.($externalId ?? 'Unknown')), 'qbo', $teamId);
    }

    /**
     * An authenticated HTTP client scoped to the connection's QBO company.
     */
    private function client(QboConnection $connection): PendingRequest
    {
        $connection = $this->refreshIfNeeded($connection);

        return Http::withToken($connection->access_token)
            ->acceptJson()
            ->baseUrl($this->cfg['api_base_url'].'/v3/company/'.$connection->realm_id);
    }

    private function refreshIfNeeded(QboConnection $connection): QboConnection
    {
        if ($connection->token_expires_at && $connection->token_expires_at->isFuture()) {
            return $connection;
        }

        $tokens = $this->requestTokens([
            'grant_type' => 'refresh_token',
            'refresh_token' => $connection->refresh_token,
        ]);

        $connection->update([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'] ?? $connection->refresh_token,
            'token_expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 3600)),
        ]);

        return $connection;
    }

    /**
     * @param  array<string, string>  $form
     * @return array<string, mixed>
     */
    private function requestTokens(array $form): array
    {
        return $this->requestProviderTokens($this->cfg, $form);
    }
}
