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
use App\Models\SageConnection;
use App\Models\Vendor;
use App\Services\Concerns\RequestsProviderTokens;
use App\Services\Concerns\ResolvesSyncedContacts;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Two-way sync with Sage Accounting via OAuth 2.0. Shares the OAuth token
 * request and contact-resolution helpers with QBO/Xero via the Concerns traits;
 * provider-specific endpoints/payloads stay here.
 */
class SageService
{
    use RequestsProviderTokens;
    use ResolvesSyncedContacts;

    /** @var array<string, mixed> */
    private array $cfg;

    public function __construct()
    {
        $this->cfg = config('services.sage');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return $this->cfg['authorization_url'].'?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $this->cfg['client_id'],
            'redirect_uri' => $this->cfg['redirect_uri'],
            'scope' => 'full_access',
            'state' => $state,
        ]);
    }

    /**
     * Exchange the code for tokens, resolve the Sage business, persist the connection.
     */
    public function handleCallback(int $userId, string $code): SageConnection
    {
        $tokens = $this->requestProviderTokens($this->cfg, [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->cfg['redirect_uri'],
        ]);

        $businessId = Http::withToken($tokens['access_token'])
            ->acceptJson()
            ->get($this->cfg['businesses_url'])
            ->throw()
            ->json()['$items'][0]['id'] ?? null;

        return SageConnection::updateOrCreate(
            ['user_id' => $userId, 'business_id' => $businessId],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'token_expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 3600)),
                'status' => 'active',
            ],
        );
    }

    public function pushInvoice(Invoice $invoice, SageConnection $connection): Invoice
    {
        $payload = ['sales_invoice' => [
                'contact_id' => (string) (Customer::whereKey($invoice->customer_id)->value('sage_id') ?? ''),
            'date' => optional($invoice->invoice_date)->format('Y-m-d') ?? (string) $invoice->invoice_date,
            'invoice_lines' => [[
                'description' => 'Invoice '.($invoice->invoice_number ?? $invoice->id),
                'quantity' => 1,
                'unit_price' => (float) $invoice->total_amount,
            ]],
        ]];

        $body = $this->client($connection)->post('/sales_invoices', $payload)->throw()->json();

        $invoice->update(['sage_id' => $body['id'] ?? $invoice->sage_id]);

        return $invoice;
    }

    public function pullInvoices(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/sales_invoices')->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            Invoice::updateOrCreate(
                ['sage_id' => $row['id'], 'team_id' => $connection->team_id],
                [
                    'invoice_number' => $row['displayed_as'] ?? ('SAGE-'.$row['id']),
                    'customer_id' => $this->resolveCustomerId($row['contact_id'] ?? null, $row['contact_name'] ?? null, $connection->team_id),
                    'total_amount' => $row['total_amount'] ?? 0,
                    'invoice_date' => $row['date'] ?? now()->toDateString(),
                    'payment_status' => 'pending',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    /**
     * Synchronize the Sage entities supported by the accounting application.
     *
     * @return array{customers:int,vendors:int,accounts:int,invoices:int,bills:int,payments:int,estimates:int,credit_memos:int,transactions:int}
     */
    public function sync(SageConnection $connection): array
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

    public function pullTransactions(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/bank_transactions')->throw()->json()['$items'] ?? [];

        return app(ProviderTransactionImporter::class)->handle($rows, $connection->team_id, function (array $row) use ($connection): array {
            $amount = (float) ($row['amount'] ?? $row['total_amount'] ?? 0);
            if (strtolower((string) ($row['transaction_type'] ?? $row['type'] ?? 'receive')) === 'payment') {
                $amount *= -1;
            }

            return [
                'external_id' => 'sage:bank:'.(string) $row['id'],
                'date' => (string) ($row['date'] ?? now()->toDateString()),
                'amount' => $amount,
                'description' => (string) ($row['description'] ?? $row['reference'] ?? 'Sage bank transaction'),
                'account_id' => app(ProviderTransactionImporter::class)->accountId('sage_id', $row['bank_account_id'] ?? null, $connection->team_id),
                'type' => strtolower((string) ($row['transaction_type'] ?? $row['type'] ?? 'bank')),
            ];
        });
    }

    public function pushEstimate(Estimate $estimate, SageConnection $connection): Estimate
    {
        $body = $this->client($connection)->post('/quotes', [
            'quote' => [
                'contact_id' => (string) (Customer::whereKey($estimate->customer_id)->value('sage_id') ?? ''),
                'date' => optional($estimate->estimate_date)->format('Y-m-d') ?? (string) $estimate->estimate_date,
                'expiry_date' => optional($estimate->expiration_date)->format('Y-m-d'),
                'reference' => $estimate->estimate_number,
                'quote_lines' => [[
                    'description' => 'Estimate '.($estimate->estimate_number ?? $estimate->getKey()),
                    'quantity' => 1,
                    'unit_price' => (float) $estimate->total_amount,
                ]],
            ],
        ])->throw()->json();

        $estimate->update(['sage_id' => $body['id'] ?? $body['quote']['id'] ?? $estimate->sage_id]);

        return $estimate;
    }

    public function pullEstimates(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/quotes')->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            if (! isset($row['id'])) {
                continue;
            }

            Estimate::updateOrCreate(
                ['sage_id' => (string) $row['id'], 'team_id' => $connection->team_id],
                [
                    'customer_id' => $this->resolveCustomerId($row['contact_id'] ?? null, $row['contact_name'] ?? null, $connection->team_id),
                    'estimate_number' => $row['reference'] ?? $row['displayed_as'] ?? 'SAGE-'.$row['id'],
                    'estimate_date' => $row['date'] ?? now()->toDateString(),
                    'expiration_date' => $row['expiry_date'] ?? null,
                    'total_amount' => $row['total_amount'] ?? 0,
                    'subtotal_amount' => $row['net_amount'] ?? $row['total_amount'] ?? 0,
                    'tax_amount' => $row['tax_amount'] ?? 0,
                    'status' => 'draft',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushCreditMemo(CreditMemo $creditMemo, SageConnection $connection): CreditMemo
    {
        $body = $this->client($connection)->post('/sales_credit_notes', [
            'sales_credit_note' => [
                'contact_id' => (string) (Customer::whereKey($creditMemo->customer_id)->value('sage_id') ?? ''),
                'date' => optional($creditMemo->credit_memo_date)->format('Y-m-d') ?? (string) $creditMemo->credit_memo_date,
                'reference' => $creditMemo->credit_memo_number,
                'credit_note_lines' => [[
                    'description' => 'Credit memo '.($creditMemo->credit_memo_number ?? $creditMemo->getKey()),
                    'quantity' => 1,
                    'unit_price' => (float) $creditMemo->total_amount,
                ]],
            ],
        ])->throw()->json();

        $creditMemo->update(['sage_id' => $body['id'] ?? $body['sales_credit_note']['id'] ?? $creditMemo->sage_id]);

        return $creditMemo;
    }

    public function pullCreditMemos(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/sales_credit_notes')->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            if (! isset($row['id'])) {
                continue;
            }

            CreditMemo::updateOrCreate(
                ['sage_id' => (string) $row['id'], 'team_id' => $connection->team_id],
                [
                    'customer_id' => $this->resolveCustomerId($row['contact_id'] ?? null, $row['contact_name'] ?? null, $connection->team_id),
                    'credit_memo_number' => $row['reference'] ?? $row['displayed_as'] ?? 'SAGE-'.$row['id'],
                    'credit_memo_date' => $row['date'] ?? now()->toDateString(),
                    'total_amount' => $row['total_amount'] ?? 0,
                    'subtotal_amount' => $row['net_amount'] ?? $row['total_amount'] ?? 0,
                    'tax_amount' => $row['tax_amount'] ?? 0,
                    'status' => 'open',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushCustomer(Customer $customer, SageConnection $connection): Customer
    {
        $body = $this->client($connection)->post('/contacts', [
            'contact' => [
                'name' => trim($customer->customer_name.' '.$customer->customer_last_name),
                'contact_type_ids' => ['CUSTOMER'],
                'email' => $customer->customer_email,
                'telephone' => $customer->customer_phone,
            ],
        ])->throw()->json();

        $customer->update(['sage_id' => $body['id'] ?? $body['contact']['id'] ?? $customer->sage_id]);

        return $customer;
    }

    public function pullCustomers(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/contacts', ['contact_type' => 'customer'])->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            $this->upsertCustomer($row, $connection->team_id);
        }

        return count($rows);
    }

    public function pushVendor(Vendor $vendor, SageConnection $connection): Vendor
    {
        $body = $this->client($connection)->post('/contacts', [
            'contact' => [
                'name' => $vendor->name,
                'contact_type_ids' => ['SUPPLIER'],
                'email' => $vendor->email,
                'telephone' => $vendor->phone,
            ],
        ])->throw()->json();

        $vendor->update(['sage_id' => $body['id'] ?? $body['contact']['id'] ?? $vendor->sage_id]);

        return $vendor;
    }

    public function pullVendors(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/contacts', ['contact_type' => 'supplier'])->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            $id = $row['id'] ?? null;
            if ($id === null) {
                continue;
            }

            Vendor::updateOrCreate(
                ['sage_id' => (string) $id, 'team_id' => $connection->team_id],
                [
                    'name' => $row['name'] ?? $row['displayed_as'] ?? 'Sage Vendor '.$id,
                    'email' => $row['email'] ?? 'sage-vendor-'.$id.'@imported.invalid',
                    'phone' => $row['telephone'] ?? null,
                    'address' => $row['address']['address_line_1'] ?? 'Imported from Sage',
                    'team_id' => $connection->team_id,
                ],
            );
        }

        return count($rows);
    }

    public function pushAccount(Account $account, SageConnection $connection): Account
    {
        $body = $this->client($connection)->post('/ledger_accounts', [
            'ledger_account' => [
                'name' => $account->account_name,
                'displayed_as' => $account->account_name,
                'nominal_code' => (string) $account->account_number,
                'ledger_account_type_id' => $this->ledgerAccountType($account->account_type),
            ],
        ])->throw()->json();

        $account->update(['sage_id' => $body['id'] ?? $body['ledger_account']['id'] ?? $account->sage_id]);

        return $account;
    }

    public function pullAccounts(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/ledger_accounts')->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            $externalId = $row['id'] ?? null;
            if ($externalId === null) {
                continue;
            }

            Account::updateOrCreate(
                ['sage_id' => (string) $externalId, 'team_id' => $connection->team_id],
                [
                    'account_name' => $row['name'] ?? $row['displayed_as'] ?? 'Sage Account '.$externalId,
                    'account_type' => $this->localAccountType($row),
                    'account_number' => is_numeric($row['nominal_code'] ?? null)
                        ? (int) $row['nominal_code']
                        : 9000 + (crc32((string) $externalId) % 1000),
                    'is_active' => ! (($row['is_active'] ?? true) === false),
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    public function pushBill(Bill $bill, SageConnection $connection): Bill
    {
        $body = $this->client($connection)->post('/purchase_invoices', [
            'purchase_invoice' => [
                'contact_id' => (string) (Vendor::whereKey($bill->vendor_id)->value('sage_id') ?? ''),
                'date' => optional($bill->bill_date)->format('Y-m-d') ?? (string) $bill->bill_date,
                'due_date' => optional($bill->due_date)->format('Y-m-d') ?? (string) $bill->due_date,
                'reference' => $bill->bill_number,
                'invoice_lines' => [[
                    'description' => 'Bill '.($bill->bill_number ?? $bill->getKey()),
                    'quantity' => 1,
                    'unit_price' => (float) $bill->total_amount,
                ]],
            ],
        ])->throw()->json();

        $bill->update(['sage_id' => $body['id'] ?? $body['purchase_invoice']['id'] ?? $bill->sage_id]);

        return $bill;
    }

    public function pullBills(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/purchase_invoices')->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            $externalId = $row['id'] ?? null;
            if ($externalId === null) {
                continue;
            }

            Bill::updateOrCreate(
                ['sage_id' => (string) $externalId, 'team_id' => $connection->team_id],
                [
                    'bill_number' => $row['reference'] ?? $row['displayed_as'] ?? 'SAGE-'.$externalId,
                    'vendor_id' => $this->resolveVendorId($row['contact_id'] ?? null, $row['contact_name'] ?? null, $connection->team_id),
                    'total_amount' => $row['total_amount'] ?? 0,
                    'subtotal_amount' => $row['net_amount'] ?? $row['total_amount'] ?? 0,
                    'tax_amount' => $row['tax_amount'] ?? 0,
                    'bill_date' => $row['date'] ?? now()->toDateString(),
                    'due_date' => $row['due_date'] ?? ($row['date'] ?? now()->toDateString()),
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    public function pushPayment(Payment $payment, SageConnection $connection): Payment
    {
        $invoice = Invoice::find($payment->invoice_id);
        $body = $this->client($connection)->post('/contact_payments', [
            'contact_payment' => [
                'contact_id' => $invoice === null ? '' : (Customer::whereKey($invoice->customer_id)->value('sage_id') ?? ''),
                'date' => optional($payment->payment_date)->format('Y-m-d') ?? (string) $payment->payment_date,
                'total_amount' => (float) $payment->payment_amount,
                'payment_type_id' => '1',
            ],
        ])->throw()->json();

        $payment->update(['sage_id' => $body['id'] ?? $body['contact_payment']['id'] ?? $payment->sage_id]);

        return $payment;
    }

    public function pullPayments(SageConnection $connection): int
    {
        $rows = $this->client($connection)->get('/contact_payments')->throw()->json()['$items'] ?? [];

        foreach ($rows as $row) {
            $externalId = $row['id'] ?? null;
            if ($externalId === null) {
                continue;
            }

            $externalInvoiceId = $row['invoice_id'] ?? null;
            if (! is_string($externalInvoiceId) || $externalInvoiceId === '') {
                continue;
            }
            $invoiceId = Invoice::where('sage_id', $externalInvoiceId)->where('team_id', $connection->team_id)->value('id');
            if ($invoiceId === null) {
                continue;
            }

            Payment::updateOrCreate(
                ['sage_id' => (string) $externalId, 'team_id' => $connection->team_id],
                [
                    'invoice_id' => (int) $invoiceId,
                    'payment_amount' => $row['total_amount'] ?? 0,
                    'payment_date' => $row['date'] ?? now()->toDateString(),
                    'team_id' => $connection->team_id,
                ],
            );
        }

        $connection->update(['last_synced_at' => now()]);

        return count($rows);
    }

    private function ledgerAccountType(string $accountType): string
    {
        return match ($accountType) {
            'liability' => 'liability',
            'equity' => 'equity',
            'revenue', 'income' => 'income',
            'expense' => 'expense',
            default => 'asset',
        };
    }

    /** @param array<string,mixed> $row */
    private function localAccountType(array $row): string
    {
        return match (strtolower((string) ($row['ledger_account_type']['name'] ?? $row['ledger_account_type'] ?? 'asset'))) {
            'liability', 'current liability', 'long term liability' => 'liability',
            'equity' => 'equity',
            'income', 'revenue' => 'revenue',
            'expense', 'cost of sales' => 'expense',
            default => 'asset',
        };
    }

    private function client(SageConnection $connection): PendingRequest
    {
        $connection = $this->refreshIfNeeded($connection);

        return Http::withToken($connection->access_token)
            ->acceptJson()
            ->baseUrl($this->cfg['api_base_url']);
    }

    private function refreshIfNeeded(SageConnection $connection): SageConnection
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
            'token_expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 3600)),
            'status' => 'active',
        ]);

        return $connection;
    }

    private function resolveCustomerId(?string $externalId, ?string $contactName = null, ?int $teamId = null): int
    {
        if ($externalId !== null && ($localId = Customer::where('sage_id', $externalId)->where('team_id', $teamId)->value('id')) !== null) {
            return (int) $localId;
        }

        return $this->syncedCustomerId($contactName ?? 'Sage Customer', 'sage', $teamId);
    }

    /** @param array<string,mixed> $row */
    private function upsertCustomer(array $row, ?int $teamId = null): void
    {
        $id = $row['id'] ?? null;
        if ($id === null) {
            return;
        }

        Customer::updateOrCreate(
            ['sage_id' => (string) $id, 'team_id' => $teamId],
            [
                'customer_name' => $row['name'] ?? $row['displayed_as'] ?? 'Sage Customer '.$id,
                'customer_last_name' => '',
                'customer_email' => $row['email'] ?? 'sage-'.$id.'@imported.invalid',
                'customer_phone' => $row['telephone'] ?? 'sage-'.$id,
                'customer_address' => $row['address']['address_line_1'] ?? 'Imported from Sage',
                'customer_city' => $row['address']['city'] ?? 'Unknown',
                'team_id' => $teamId,
            ],
        );
    }

    private function resolveVendorId(?string $externalId, ?string $contactName = null, ?int $teamId = null): int
    {
        if ($externalId !== null && ($localId = Vendor::where('sage_id', $externalId)->where('team_id', $teamId)->value('vendor_id')) !== null) {
            return (int) $localId;
        }

        return $this->syncedVendorId($contactName ?? 'Sage Vendor', 'sage', $teamId);
    }
}
