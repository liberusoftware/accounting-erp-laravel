<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Liberu\Accounting\BankAccounts\Actions\CreateBankAccount;
use Liberu\Accounting\BankAccounts\Actions\SetBankAccountStatus;
use Liberu\Accounting\BankAccounts\Enums\BankAccountStatus;
use Liberu\Accounting\BankAccounts\Enums\BankAccountType;
use Liberu\Accounting\BankAccounts\Events\BankAccountCreated;
use Liberu\Accounting\BankAccounts\Events\BankAccountStatusChanged;
use Liberu\Accounting\BankAccounts\Exceptions\InvalidBankAccount;
use Tests\TestCase;

final class AccountingBankAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_lifecycle_owns_currency_opening_data_and_restricted_details(): void
    {
        Event::fake([BankAccountCreated::class, BankAccountStatusChanged::class]);
        $entityId = DB::table('accounting_legal_entities')->insertGetId(['name' => 'Bank Entity', 'currency_code' => 'GBP', 'created_at' => now(), 'updated_at' => now()]);

        $account = app(CreateBankAccount::class)->handle([
            'legal_entity_id' => $entityId,
            'name' => 'Operating account',
            'institution_name' => 'Example Bank',
            'account_type' => BankAccountType::Current->value,
            'currency' => 'gbp',
            'opening_balance' => 1000,
            'opening_date' => '2026-08-01',
            'account_number' => 'secret-account-number',
            'routing_number' => 'secret-routing-number',
        ]);

        $this->assertSame('GBP', $account->currency);
        $this->assertSame(1000.0, (float) $account->current_balance);
        $this->assertNull($account->toArray()['account_number'] ?? null);
        Event::assertDispatched(BankAccountCreated::class);

        $account->current_balance = 0;
        $account->save();
        app(SetBankAccountStatus::class)->handle($account, BankAccountStatus::Closed);

        $this->assertSame(BankAccountStatus::Closed, $account->fresh()->status);
        Event::assertDispatched(BankAccountStatusChanged::class);
    }

    public function test_non_zero_accounts_cannot_be_closed_and_names_are_unique_per_entity(): void
    {
        $entityId = DB::table('accounting_legal_entities')->insertGetId(['name' => 'Duplicate Bank Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now()]);
        $attributes = ['legal_entity_id' => $entityId, 'name' => 'Reserve account', 'account_type' => BankAccountType::Savings->value, 'currency' => 'USD', 'opening_balance' => 10, 'opening_date' => '2026-08-01'];
        $account = app(CreateBankAccount::class)->handle($attributes);

        try {
            app(SetBankAccountStatus::class)->handle($account, BankAccountStatus::Closed);
            $this->fail('Expected a non-zero account to be rejected when closing.');
        } catch (InvalidBankAccount) {
            $this->assertTrue(true);
        }

        $this->expectException(InvalidBankAccount::class);
        app(CreateBankAccount::class)->handle($attributes);
    }
}
