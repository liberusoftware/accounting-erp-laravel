<?php

declare(strict_types=1);

use App\Models\CreditMemo;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\QboConnection;
use App\Models\SageConnection;
use App\Models\User;
use App\Models\XeroConnection;
use App\Services\QuickBooksService;
use App\Services\SageService;
use App\Services\XeroService;
use Illuminate\Support\Facades\Http;
use Liberu\Foundation\Organizations\Models\Team;

it('keeps provider credit memos and their contacts within the connection team', function (string $provider, string $connectionClass, string $accountKey, string $serviceClass): void {
    $user = User::factory()->create();
    $ownTeam = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $column = $provider.'_id';
    $otherCustomer = Customer::factory()->create(['team_id' => $otherTeam->id, $column => 'remote-customer']);
    $otherMemo = CreditMemo::query()->create(['team_id' => $otherTeam->id, 'customer_id' => $otherCustomer->id, $column => 'shared', 'credit_memo_number' => 'OTHER', 'credit_memo_date' => '2026-10-25', 'total_amount' => 99, 'subtotal_amount' => 99]);
    $connection = $connectionClass::query()->create(['user_id' => $user->id, 'team_id' => $ownTeam->id, $accountKey => 'own-account', 'access_token' => 'access', 'refresh_token' => 'refresh', 'token_expires_at' => now()->addHour(), 'status' => 'active']);
    $response = match ($provider) {
        'qbo' => ['QueryResponse' => ['CreditMemo' => [['Id' => 'shared', 'DocNumber' => 'OWN', 'TxnDate' => '2026-10-25', 'TotalAmt' => 20, 'CustomerRef' => ['value' => 'remote-customer', 'name' => 'Own customer']]]]],
        'xero' => ['CreditNotes' => [['CreditNoteID' => 'shared', 'CreditNoteNumber' => 'OWN', 'Date' => '2026-10-25', 'Total' => 20, 'Contact' => ['ContactID' => 'remote-customer', 'Name' => 'Own customer']]]],
        'sage' => ['$items' => [['id' => 'shared', 'reference' => 'OWN', 'date' => '2026-10-25', 'total_amount' => 20, 'contact_id' => 'remote-customer', 'contact_name' => 'Own customer']]],
    };
    Http::fake(['*' => Http::response($response)]);
    $service = app($serviceClass);
    expect($service->pullCreditMemos($connection))->toBe(1);
    $service->pullCreditMemos($connection);
    $memo = CreditMemo::query()->where('team_id', $ownTeam->id)->where($column, 'shared')->firstOrFail();
    expect($memo->customer->team_id)->toBe($ownTeam->id)->and($memo->customer_id)->not->toBe($otherCustomer->id)
        ->and((float) $otherMemo->fresh()->total_amount)->toBe(99.0)
        ->and($otherMemo->fresh()->team_id)->toBe($otherTeam->id)
        ->and(CreditMemo::query()->where($column, 'shared')->count())->toBe(2);
})->with([
    ['qbo', QboConnection::class, 'realm_id', QuickBooksService::class],
    ['xero', XeroConnection::class, 'tenant_id', XeroService::class],
    ['sage', SageConnection::class, 'business_id', SageService::class],
]);

it('does not attach provider payments to foreign or unspecified invoices', function (string $provider, string $connectionClass, string $accountKey, string $serviceClass): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $column = $provider.'_id';
    $otherCustomer = Customer::factory()->create(['team_id' => $otherTeam->id]);
    $otherInvoice = Invoice::factory()->create(['team_id' => $otherTeam->id, 'customer_id' => $otherCustomer->id, $column => 'foreign-invoice']);
    Invoice::factory()->create(['team_id' => $team->id, $column => null]);
    $connection = $connectionClass::query()->create(['user_id' => $user->id, 'team_id' => $team->id, $accountKey => 'own-account', 'access_token' => 'access', 'refresh_token' => 'refresh', 'token_expires_at' => now()->addHour(), 'status' => 'active']);
    $response = fn (?string $invoiceId): array => match ($provider) {
        'qbo' => ['QueryResponse' => ['Payment' => [['Id' => 'foreign-payment', 'TotalAmt' => 20, 'TxnDate' => '2026-10-25', 'Line' => [['LinkedTxn' => [['TxnId' => $invoiceId]]]]]]]],
        'xero' => ['Payments' => [['PaymentID' => 'foreign-payment', 'Amount' => 20, 'Date' => '2026-10-25', 'Invoice' => ['InvoiceID' => $invoiceId]]]],
        'sage' => ['$items' => [['id' => 'foreign-payment', 'amount' => 20, 'date' => '2026-10-25', 'invoice_id' => $invoiceId]]],
    };
    Http::fake(['*' => Http::sequence()->push($response('foreign-invoice'))->push($response(null))]);
    $service = app($serviceClass);
    $service->pullPayments($connection);
    $service->pullPayments($connection);
    expect(Payment::query()->count())->toBe(0)
        ->and($otherInvoice->fresh()->team_id)->toBe($otherTeam->id);
})->with([
    ['qbo', QboConnection::class, 'realm_id', QuickBooksService::class],
    ['xero', XeroConnection::class, 'tenant_id', XeroService::class],
    ['sage', SageConnection::class, 'business_id', SageService::class],
]);
