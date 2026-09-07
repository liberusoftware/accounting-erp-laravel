<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankAccounts\Actions\CreateBankAccount;
use Liberu\Accounting\BankAccounts\Enums\BankAccountType;
use Liberu\Accounting\BankFeeds\Actions\CreateBankFeedConnection;
use Liberu\Accounting\BankFeeds\Actions\ImportBankFeedTransactions;
use Liberu\Accounting\BankFeeds\Actions\MapBankFeedAccount;
use Liberu\Accounting\BankFeeds\Actions\UpsertBankFeedInstitution;
use Liberu\Accounting\BankFeeds\Enums\FeedTransactionStatus;
use Liberu\Accounting\BankFeeds\Models\BankFeedTransaction;
use Tests\TestCase;

final class AccountingBankFeedsTest extends TestCase
{
    use RefreshDatabase;

    public function test_connection_mapping_and_import_are_idempotent_and_protect_payloads(): void
    {
        $entityId = DB::table('accounting_legal_entities')->insertGetId(['name' => 'Feed Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now()]);
        $account = app(CreateBankAccount::class)->handle(['legal_entity_id' => $entityId, 'name' => 'Checking', 'account_type' => BankAccountType::Current->value, 'currency' => 'USD', 'opening_balance' => 0, 'opening_date' => '2026-08-01']);
        $institution = app(UpsertBankFeedInstitution::class)->handle(['provider' => 'plaid', 'external_id' => 'ins_1', 'name' => 'Example Bank']);
        $connection = app(CreateBankFeedConnection::class)->handle(['institution_id' => $institution->id, 'provider' => 'plaid', 'name' => 'Example connection', 'external_reference' => 'item_1', 'access_token' => 'do-not-expose']);
        $mapping = app(MapBankFeedAccount::class)->handle($connection, ['bank_account_id' => $account->id, 'external_account_id' => 'acct_1', 'name' => 'Checking', 'currency' => 'usd']);

        $batch = ['added' => [['external_id' => 'txn_1', 'mapping_id' => $mapping->id, 'transaction_date' => '2026-08-20', 'amount' => '-12.50', 'currency' => 'usd', 'status' => 'pending', 'description' => 'Coffee', 'raw_data' => ['secret' => 'value']]], 'next_cursor' => 'cursor-2'];
        $this->assertSame(['imported' => 1, 'duplicates' => 0], app(ImportBankFeedTransactions::class)->handle($connection, $batch));
        $this->assertSame(['imported' => 1, 'duplicates' => 1], app(ImportBankFeedTransactions::class)->handle($connection, ['modified' => [array_merge($batch['added'][0], ['status' => 'posted'])], 'next_cursor' => 'cursor-3']));

        $transaction = BankFeedTransaction::query()->firstOrFail();
        $this->assertSame(FeedTransactionStatus::Posted, $transaction->status);
        $this->assertSame('cursor-3', $connection->fresh()->cursor);
        $this->assertNull($connection->fresh()->toArray()['access_token'] ?? null);
        $this->assertNull($transaction->toArray()['raw_data'] ?? null);
        $this->assertSame(1, BankFeedTransaction::query()->count());
    }

    public function test_removed_transactions_are_marked_without_deleting_history(): void
    {
        $institution = app(UpsertBankFeedInstitution::class)->handle(['provider' => 'wise', 'external_id' => 'wise_1', 'name' => 'Wise']);
        $connection = app(CreateBankFeedConnection::class)->handle(['institution_id' => $institution->id, 'provider' => 'wise', 'name' => 'Wise connection', 'external_reference' => 'profile_1', 'access_token' => 'secret']);
        app(ImportBankFeedTransactions::class)->handle($connection, ['added' => [['external_id' => 'transfer_1', 'transaction_date' => '2026-08-20', 'amount' => '4.00', 'currency' => 'EUR']], 'next_cursor' => 'one']);
        app(ImportBankFeedTransactions::class)->handle($connection, ['removed' => ['transfer_1'], 'next_cursor' => 'two']);

        $this->assertSame(FeedTransactionStatus::Removed, BankFeedTransaction::query()->firstOrFail()->status);
        $this->assertSame(1, BankFeedTransaction::query()->count());
    }
}
