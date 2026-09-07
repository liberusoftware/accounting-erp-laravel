<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;
use Liberu\Accounting\BankFeeds\Models\BankFeedInstitution;
use Liberu\Accounting\Plaid\PlaidBankFeedAdapter;
use Liberu\Accounting\Revolut\RevolutAdapter;
use Liberu\Accounting\Wise\WiseBankFeedAdapter;
use Tests\TestCase;

final class AccountingProviderAdaptersTest extends TestCase
{
    use RefreshDatabase;

    public function test_plaid_normalizes_incremental_transactions_and_removed_ids(): void
    {
        Http::fake(['*plaid.com/transactions/sync' => Http::response(['added' => [['transaction_id' => 'txn-1', 'date' => '2026-08-25', 'amount' => 25, 'pending' => true, 'name' => 'Coffee', 'category' => ['Food', 'Coffee Shops']]], 'modified' => [], 'removed' => [['transaction_id' => 'txn-old']], 'next_cursor' => 'next'], 200)]);
        $connection = $this->connection('plaid', 'plaid-item', ['access_token' => 'plaid-secret']);

        $batch = app(PlaidBankFeedAdapter::class)->fetch($connection);

        $this->assertSame('txn-1', $batch['added'][0]['external_id']);
        $this->assertSame(-25.0, (float) $batch['added'][0]['amount']);
        $this->assertSame('pending', $batch['added'][0]['status']);
        $this->assertSame(['txn-old'], $batch['removed']);
        $this->assertSame('next', $batch['next_cursor']);
    }

    public function test_wise_normalizes_transfers_and_revolut_uses_payment_contract(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), 'transferwise.tech/v1/transfers')) {
                return $request->method() === 'POST'
                    ? Http::response(['id' => 43], 201)
                    : Http::response([['id' => 42, 'createdDate' => '2026-08-25T10:00:00Z', 'targetValue' => 50, 'targetCurrency' => 'EUR', 'status' => 'outgoing_payment_sent', 'reference' => 'Supplier']], 200);
            }
            if (str_contains($request->url(), 'revolut.com/api/1.0/pay')) {
                return Http::response(['id' => 'payment-1'], 201);
            }

            return Http::response([], 200);
        });
        $wise = $this->connection('wise', 'wise-profile', ['access_token' => 'wise-secret', 'credentials' => ['profile_id' => 99]]);
        $wiseBatch = app(WiseBankFeedAdapter::class)->fetch($wise);
        $this->assertSame('42', $wiseBatch['added'][0]['external_id']);
        $this->assertSame('EUR', $wiseBatch['added'][0]['currency']);
        $wisePayment = (new WiseBankFeedAdapter($wise))->sendPayment(['sourceCurrency' => 'EUR', 'targetCurrency' => 'EUR', 'targetValue' => 10]);
        $this->assertSame(43, $wisePayment['id']);
        $wiseBulk = (new WiseBankFeedAdapter($wise))->sendBulkPayments('Test batch', [['sourceCurrency' => 'EUR', 'targetCurrency' => 'EUR', 'targetValue' => 5]], '2026-09-15');
        $this->assertCount(1, $wiseBulk['payments']);
        Http::assertSent(fn ($request): bool => $request->method() === 'POST'
            && str_contains($request->url(), 'transferwise.tech/v1/transfers')
            && ($request['schedule_for'] ?? null) === '2026-09-15');

        $revolutConnection = $this->connection('revolut', 'revolut-account', ['access_token' => 'revolut-secret']);
        $payment = (new RevolutAdapter($revolutConnection))->sendPayment(['amount' => 10, 'currency' => 'EUR', 'reference' => 'Test']);
        $this->assertSame('payment-1', $payment['id']);
    }

    private function connection(string $provider, string $reference, array $attributes): BankFeedConnection
    {
        $institution = BankFeedInstitution::query()->create(['provider' => $provider, 'external_id' => $reference.'-institution', 'name' => ucfirst($provider)]);

        return BankFeedConnection::query()->create(array_merge(['institution_id' => $institution->id, 'provider' => $provider, 'name' => ucfirst($provider), 'external_reference' => $reference, 'access_token' => 'secret'], $attributes));
    }
}
