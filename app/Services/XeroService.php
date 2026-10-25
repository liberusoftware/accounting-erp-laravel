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
use App\Models\Vendor;
use App\Models\XeroConnection;
use App\Services\Concerns\RequestsProviderTokens;
use App\Services\Concerns\ResolvesSyncedContacts;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Two-way sync with Xero via OAuth 2.0. Shares the OAuth token request and
 * contact-resolution helpers with the other providers via the Concerns traits;
 * provider-specific endpoints/payloads stay here.
 */
class XeroService
{
    use RequestsProviderTokens;
    use ResolvesSyncedContacts;

    /** @var array<string, mixed> */
    private array $cfg;

    public function __construct()
    {
        $this->cfg = config('services.xero');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return $this->cfg['authorization_url'].'?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $this->cfg['client_id'],
            'redirect_uri' => $this->cfg['redirect_uri'],
            'scope' => 'offline_access accounting.transactions accounting.contacts',
            'state' => $state,
        ]);
    }

    /**
     * Exchange the code for tokens, resolve the Xero tenant, persist the connection.
     */
    public function handleCallback(int $userId, string $code): XeroConnection
    {
        $tokens = $this->requestProviderTokens($this->cfg, [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->cfg['redirect_uri'],
        ]);

        $tenantId = Http::withToken($tokens['access_token'])
            ->acceptJson()
            ->get($this->cfg['connections_url'])
            ->throw()
            ->json()[0]['tenantId'] ?? null;

        return XeroConnection::updateOrCreate(
            ['user_id' => $userId, 'tenant_id' => $tenantId],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'token_expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 1800)),
                'status' => 'active',
            ],
        );
    }

    public function pushInvoice(Invoice $invoice, XeroConnection $connection): Invoice
    {
        $payload = ['Invoices' => [[
            'Type' => 'ACCREC',
            'Contact' => $invoice->customer?->xero_id
                ? ['ContactID' => $invoice->customer->xero_id]
                : ['Name' => 'Customer '.$invoice->customer_id],
            'LineItems' => [[
                'Description' => 'Invoice '.($invoice->invoice_number ?? $invoice->id),
                'Quantity' => 1,
                'UnitAmount' => (float) $invoice->total_amount,
                'AccountCode' => '200',
            ]],
            'Status' => 'AUTHORISED',
        ]]];

        if ($invoice->xero_id) {
            $payload['Invoices'][0]['InvoiceID'] = $invoice->xero_id;
        }

        $body = $this->client($connection)->post('/Invoices', $payload)->throw()->json();

        $invoice->update(['xero_id' => $body['Invoices'][0]['InvoiceID'] ?? $invoice->xero_id]);

        return $invoice;
    }

    public function pullInvoices(XeroConnection $connection): int
    {
        $rows = $this->client($connection)
            ->get('/Invoices', ['where' => 'Type=="ACCREC"'])
            ->throw()
            ->json()['Invoices'] ?? [];

        foreach ($rows as $row) {
            Invoice::updateOrCreate(
                ['xero_id' => $row['InvoiceID'], 'team_id' => $connection->team_id],
                [
                    'invoice_number' => $row['InvoiceNumber'] ?? ('XERO-'.$row['InvoiceID']),
                    'customer_id' => $this->resolveCustomerId($row['Contact'] ?? null, $connection->team_id),
                    'total_amount' => $row['Total'] ?? 0,
                    'invoice_date' => $row['Date'] ?? now()->toDateString(),
                    'payment_status' => 'pending',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    /**
     * Pull every accounting entity currently supported by the Xero adapter.
     *
     * @return array{customers:int,vendors:int,accounts:int,invoices:int,bills:int,payments:int,estimates:int,credit_memos:int,transactions:int}
     */
    public function sync(XeroConnection $connection): array
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

    public function pullTransactions(XeroConnection $connection): int
    {
        $rows = $this->client($connection)->get('/BankTransactions')->throw()->json()['BankTransactions'] ?? [];

        return app(ProviderTransactionImporter::class)->handle($rows, $connection->team_id, function (array $row) use ($connection): array {
            $type = strtoupper((string) ($row['Type'] ?? 'RECEIVE'));
            $amount = (float) ($row['Total'] ?? 0);
            if ($type === 'SPEND') {
                $amount *= -1;
            }

            return [
                'external_id' => 'xero:bank:'.(string) $row['BankTransactionID'],
                'date' => (string) ($row['Date'] ?? now()->toDateString()),
                'amount' => $amount,
                'description' => (string) ($row['Reference'] ?? 'Xero bank transaction'),
                'account_id' => app(ProviderTransactionImporter::class)->accountId('xero_id', $row['BankAccount']['AccountID'] ?? null, $connection->team_id),
                'type' => strtolower($type),
            ];
        });
    }

    public function pushEstimate(Estimate $estimate, XeroConnection $connection): Estimate
    {
        $payload = ['Quotes' => [[
            'QuoteNumber' => $estimate->estimate_number,
            'Date' => optional($estimate->estimate_date)->format('Y-m-d') ?? (string) $estimate->estimate_date,
            'ExpiryDate' => optional($estimate->expiration_date)->format('Y-m-d'),
            'Contact' => ['ContactID' => Customer::whereKey($estimate->customer_id)->value('xero_id')],
            'LineItems' => [[
                'Description' => 'Estimate '.($estimate->estimate_number ?? $estimate->getKey()),
                'Quantity' => 1,
                'UnitAmount' => (float) $estimate->total_amount,
                'AccountCode' => '200',
            ]],
            'Status' => 'DRAFT',
        ]]];

        if ($estimate->xero_id) {
            $payload['Quotes'][0]['QuoteID'] = $estimate->xero_id;
        }

        $body = $this->client($connection)->post('/Quotes', $payload)->throw()->json();

        $estimate->update(['xero_id' => $body['Quotes'][0]['QuoteID'] ?? $estimate->xero_id]);

        return $estimate;
    }

    public function pullEstimates(XeroConnection $connection): int
    {
        $rows = $this->client($connection)->get('/Quotes')->throw()->json()['Quotes'] ?? [];

        foreach ($rows as $row) {
            if (! isset($row['QuoteID'])) {
                continue;
            }

            Estimate::updateOrCreate(
                ['xero_id' => (string) $row['QuoteID'], 'team_id' => $connection->team_id],
                [
                    'customer_id' => $this->resolveCustomerId($row['Contact'] ?? null, $connection->team_id),
                    'estimate_number' => $row['QuoteNumber'] ?? 'XERO-'.$row['QuoteID'],
                    'estimate_date' => $row['Date'] ?? now()->toDateString(),
                    'expiration_date' => $row['ExpiryDate'] ?? null,
                    'total_amount' => $row['Total'] ?? 0,
                    'subtotal_amount' => $row['SubTotal'] ?? $row['Total'] ?? 0,
                    'tax_amount' => $row['TotalTax'] ?? 0,
                    'status' => strtolower((string) ($row['Status'] ?? 'draft')),
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushCreditMemo(CreditMemo $creditMemo, XeroConnection $connection): CreditMemo
    {
        $payload = ['CreditNotes' => [[
            'Type' => 'ACCRECCREDIT',
            'Contact' => ['ContactID' => Customer::whereKey($creditMemo->customer_id)->value('xero_id')],
            'Date' => optional($creditMemo->credit_memo_date)->format('Y-m-d') ?? (string) $creditMemo->credit_memo_date,
            'CreditNoteNumber' => $creditMemo->credit_memo_number,
            'LineItems' => [[
                'Description' => 'Credit memo '.($creditMemo->credit_memo_number ?? $creditMemo->getKey()),
                'Quantity' => 1,
                'UnitAmount' => (float) $creditMemo->total_amount,
                'AccountCode' => '200',
            ]],
            'Status' => 'AUTHORISED',
        ]]];

        if ($creditMemo->xero_id) {
            $payload['CreditNotes'][0]['CreditNoteID'] = $creditMemo->xero_id;
        }

        $body = $this->client($connection)->post('/CreditNotes', $payload)->throw()->json();

        $creditMemo->update(['xero_id' => $body['CreditNotes'][0]['CreditNoteID'] ?? $creditMemo->xero_id]);

        return $creditMemo;
    }

    public function pullCreditMemos(XeroConnection $connection): int
    {
        $rows = $this->client($connection)->get('/CreditNotes')->throw()->json()['CreditNotes'] ?? [];

        foreach ($rows as $row) {
            if (! isset($row['CreditNoteID'])) {
                continue;
            }

            CreditMemo::updateOrCreate(
                ['xero_id' => (string) $row['CreditNoteID'], 'team_id' => $connection->team_id],
                [
                    'customer_id' => $this->resolveCustomerId($row['Contact'] ?? null, $connection->team_id),
                    'credit_memo_number' => $row['CreditNoteNumber'] ?? 'XERO-'.$row['CreditNoteID'],
                    'credit_memo_date' => $row['Date'] ?? now()->toDateString(),
                    'total_amount' => $row['Total'] ?? 0,
                    'subtotal_amount' => $row['SubTotal'] ?? $row['Total'] ?? 0,
                    'tax_amount' => $row['TotalTax'] ?? 0,
                    'status' => 'open',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushCustomer(Customer $customer, XeroConnection $connection): Customer
    {
        $body = $this->client($connection)->post('/Contacts', ['Contacts' => [[
            'ContactID' => $customer->xero_id,
            'Name' => trim($customer->customer_name.' '.$customer->customer_last_name),
            'EmailAddress' => $customer->customer_email,
            'Phones' => [['PhoneType' => 'DEFAULT', 'PhoneNumber' => $customer->customer_phone]],
            'IsCustomer' => true,
        ]]])->throw()->json();

        $customer->update(['xero_id' => $body['Contacts'][0]['ContactID'] ?? $customer->xero_id]);

        return $customer;
    }

    public function pullCustomers(XeroConnection $connection): int
    {
        $rows = $this->client($connection)->get('/Contacts')->throw()->json()['Contacts'] ?? [];
        $rows = array_values(array_filter($rows, fn (array $row): bool => ($row['IsCustomer'] ?? true) === true));

        foreach ($rows as $row) {
            $id = $row['ContactID'] ?? null;
            if ($id === null) {
                continue;
            }

            Customer::updateOrCreate(
                ['xero_id' => (string) $id, 'team_id' => $connection->team_id],
                [
                    'customer_name' => $row['Name'] ?? 'Xero Customer '.$id,
                    'customer_last_name' => '',
                    'customer_email' => $row['EmailAddress'] ?? 'xero-'.$id.'@imported.invalid',
                    'customer_phone' => $row['Phones'][0]['PhoneNumber'] ?? 'xero-'.$id,
                    'customer_address' => $row['Addresses'][0]['AddressLine1'] ?? 'Imported from Xero',
                    'customer_city' => $row['Addresses'][0]['City'] ?? 'Unknown',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushVendor(Vendor $vendor, XeroConnection $connection): Vendor
    {
        $body = $this->client($connection)->post('/Contacts', ['Contacts' => [[
            'ContactID' => $vendor->xero_id,
            'Name' => $vendor->name,
            'EmailAddress' => $vendor->email,
            'Phones' => [['PhoneType' => 'DEFAULT', 'PhoneNumber' => $vendor->phone]],
            'IsSupplier' => true,
        ]]])->throw()->json();

        $vendor->update(['xero_id' => $body['Contacts'][0]['ContactID'] ?? $vendor->xero_id]);

        return $vendor;
    }

    public function pullVendors(XeroConnection $connection): int
    {
        $rows = $this->client($connection)->get('/Contacts')->throw()->json()['Contacts'] ?? [];
        $rows = array_values(array_filter($rows, fn (array $row): bool => ($row['IsSupplier'] ?? true) === true));

        foreach ($rows as $row) {
            $id = $row['ContactID'] ?? null;
            if ($id === null) {
                continue;
            }

            Vendor::updateOrCreate(
                ['xero_id' => (string) $id, 'team_id' => $connection->team_id],
                [
                    'name' => $row['Name'] ?? 'Xero Vendor '.$id,
                    'email' => $row['EmailAddress'] ?? 'xero-vendor-'.$id.'@imported.invalid',
                    'phone' => $row['Phones'][0]['PhoneNumber'] ?? null,
                    'address' => $row['Addresses'][0]['AddressLine1'] ?? 'Imported from Xero',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    /** local account_type → Xero [Type, Class]. */
    private const XERO_ACCOUNT = [
        'asset' => ['CURRENT', 'ASSET'],
        'liability' => ['CURRLIAB', 'LIABILITY'],
        'equity' => ['EQUITY', 'EQUITY'],
        'revenue' => ['REVENUE', 'REVENUE'],
        'income' => ['REVENUE', 'REVENUE'],
        'expense' => ['EXPENSE', 'EXPENSE'],
    ];

    /** Xero Account Class → local account_type. */
    private const XERO_CLASS = [
        'ASSET' => 'asset',
        'LIABILITY' => 'liability',
        'EQUITY' => 'equity',
        'REVENUE' => 'revenue',
        'EXPENSE' => 'expense',
    ];

    public function pushAccount(Account $account, XeroConnection $connection): Account
    {
        [$type] = self::XERO_ACCOUNT[$account->account_type] ?? ['CURRENT', 'ASSET'];

        $payload = ['Accounts' => [[
            'Code' => (string) $account->account_number,
            'Name' => $account->account_name,
            'Type' => $type,
        ]]];

        if ($account->xero_id) {
            $payload['Accounts'][0]['AccountID'] = $account->xero_id;
            $body = $this->client($connection)
                ->post('/Accounts/'.$account->xero_id, $payload)
                ->throw()
                ->json();
        } else {
            $body = $this->client($connection)
                ->put('/Accounts', $payload)
                ->throw()
                ->json();
        }

        $account->update(['xero_id' => $body['Accounts'][0]['AccountID'] ?? $account->xero_id]);

        return $account;
    }

    public function pullAccounts(XeroConnection $connection): int
    {
        $rows = $this->client($connection)->get('/Accounts')->throw()->json()['Accounts'] ?? [];

        foreach ($rows as $row) {
            Account::updateOrCreate(
                ['xero_id' => $row['AccountID'], 'team_id' => $connection->team_id],
                [
                    'account_name' => $row['Name'] ?? ('Xero Account '.$row['AccountID']),
                    'account_type' => self::XERO_CLASS[$row['Class'] ?? ''] ?? 'asset',
                    'account_number' => isset($row['Code']) && is_numeric($row['Code'])
                        ? (int) $row['Code']
                        : 9000 + crc32((string) $row['AccountID']) % 1000,
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    public function pushBill(Bill $bill, XeroConnection $connection): Bill
    {
        $payload = ['Invoices' => [[
            'Type' => 'ACCPAY',
            'Contact' => $bill->vendor?->xero_id
                ? ['ContactID' => $bill->vendor->xero_id]
                : ['Name' => 'Vendor '.$bill->vendor_id],
            'LineItems' => [[
                'Description' => 'Bill '.($bill->bill_number ?? $bill->getKey()),
                'Quantity' => 1,
                'UnitAmount' => (float) $bill->total_amount,
                'AccountCode' => '400',
            ]],
            'Status' => 'AUTHORISED',
        ]]];

        if ($bill->xero_id) {
            $payload['Invoices'][0]['InvoiceID'] = $bill->xero_id;
        }

        $body = $this->client($connection)->post('/Invoices', $payload)->throw()->json();

        $bill->update(['xero_id' => $body['Invoices'][0]['InvoiceID'] ?? $bill->xero_id]);

        return $bill;
    }

    public function pullBills(XeroConnection $connection): int
    {
        $rows = $this->client($connection)
            ->get('/Invoices', ['where' => 'Type=="ACCPAY"'])
            ->throw()
            ->json()['Invoices'] ?? [];

        foreach ($rows as $row) {
            Bill::updateOrCreate(
                ['xero_id' => $row['InvoiceID'], 'team_id' => $connection->team_id],
                [
                    'vendor_id' => $this->resolveVendorId($row['Contact'] ?? null, $connection->team_id),
                    'total_amount' => $row['Total'] ?? 0,
                    'bill_date' => $row['Date'] ?? now()->toDateString(),
                    'due_date' => $row['DueDate'] ?? ($row['Date'] ?? now()->toDateString()),
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    public function pushPayment(Payment $payment, XeroConnection $connection): Payment
    {
        $invoice = Invoice::find($payment->invoice_id);

        $payload = ['Payments' => [[
            'Invoice' => ['InvoiceID' => $invoice?->xero_id],
            'Account' => ['Code' => '090'],
            'Amount' => (float) $payment->payment_amount,
            'Date' => optional($payment->payment_date)->format('Y-m-d') ?? (string) $payment->payment_date,
        ]]];

        $body = $this->client($connection)->post('/Payments', $payload)->throw()->json();

        $payment->update(['xero_id' => $body['Payments'][0]['PaymentID'] ?? $payment->xero_id]);

        return $payment;
    }

    public function pullPayments(XeroConnection $connection): int
    {
        $rows = $this->client($connection)->get('/Payments')->throw()->json()['Payments'] ?? [];

        foreach ($rows as $row) {
            $externalInvoiceId = $row['Invoice']['InvoiceID'] ?? null;
            if (! is_string($externalInvoiceId) || $externalInvoiceId === '') {
                continue;
            }
            $invoiceId = Invoice::where('xero_id', $externalInvoiceId)->where('team_id', $connection->team_id)->value('id');

            if ($invoiceId === null) {
                continue;
            }

            Payment::updateOrCreate(
                ['xero_id' => $row['PaymentID'], 'team_id' => $connection->team_id],
                [
                    'invoice_id' => (int) $invoiceId,
                    'payment_amount' => $row['Amount'] ?? 0,
                    'payment_date' => $row['Date'] ?? now()->toDateString(),
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    /**
     * @param  array<string, mixed>|null  $contact
     */
    private function resolveVendorId(?array $contact, ?int $teamId = null): int
    {
        if (isset($contact['ContactID']) && ($localId = Vendor::where('xero_id', $contact['ContactID'])->where('team_id', $teamId)->value('vendor_id')) !== null) {
            return (int) $localId;
        }

        return $this->syncedVendorId($contact['Name'] ?? 'Xero Vendor', 'xero', $teamId);
    }

    private function client(XeroConnection $connection): PendingRequest
    {
        $connection = $this->refreshIfNeeded($connection);

        return Http::withToken($connection->access_token)
            ->withHeaders(['Xero-tenant-id' => $connection->tenant_id])
            ->acceptJson()
            ->baseUrl($this->cfg['api_base_url']);
    }

    private function refreshIfNeeded(XeroConnection $connection): XeroConnection
    {
        if ($connection->token_expires_at && $connection->token_expires_at->isFuture()) {
            return $connection;
        }

        $tokens = $this->requestProviderTokens($this->cfg, [
            'grant_type' => 'refresh_token',
            'refresh_token' => (string) $connection->refresh_token,
        ]);

        $connection->update([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'] ?? $connection->refresh_token,
            'token_expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 1800)),
            'status' => 'active',
        ]);

        return $connection;
    }

    /**
     * @param  array<string, mixed>|null  $contact
     */
    private function resolveCustomerId(?array $contact, ?int $teamId = null): int
    {
        if (isset($contact['ContactID']) && ($localId = Customer::where('xero_id', $contact['ContactID'])->where('team_id', $teamId)->value('id')) !== null) {
            return (int) $localId;
        }

        return $this->syncedCustomerId($contact['Name'] ?? 'Xero Customer', 'xero', $teamId);
    }
}
