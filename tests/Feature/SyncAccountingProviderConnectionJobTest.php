<?php

declare(strict_types=1);

use App\Jobs\SyncAccountingProviderConnectionJob;
use App\Models\QboConnection;
use App\Models\SageConnection;
use App\Models\User;
use App\Models\XeroConnection;
use App\Services\QuickBooksService;
use App\Services\SageService;
use App\Services\XeroService;

it('synchronizes only the selected active provider connection', function (string $provider, string $model, string $externalKey, int $serviceIndex): void {
    $connection = $model::query()->create(['user_id' => User::factory()->create()->id, $externalKey => 'provider-account', 'access_token' => 'access', 'refresh_token' => 'refresh', 'token_expires_at' => now()->addHour(), 'status' => 'active']);
    $services = [Mockery::mock(QuickBooksService::class), Mockery::mock(XeroService::class), Mockery::mock(SageService::class)];
    foreach ($services as $index => $service) {
        if ($index === $serviceIndex) {
            $service->shouldReceive('sync')->once()->withArgs(fn ($selected) => $selected instanceof $model && $selected->id === $connection->id)->andReturn([]);
        } else {
            $service->shouldNotReceive('sync');
        }
    }
    $job = new SyncAccountingProviderConnectionJob($provider, $connection->id);
    expect($job->afterCommit)->toBeTrue();
    $job->handle(...$services);
    $connection->update(['status' => 'inactive']);
    $job->handle(...$services);
    $connection->delete();
    $job->handle(...$services);
})->with([
    ['qbo', QboConnection::class, 'realm_id', 0],
    ['xero', XeroConnection::class, 'tenant_id', 1],
    ['sage', SageConnection::class, 'business_id', 2],
]);

it('rejects unsupported accounting providers', function (): void {
    $job = new SyncAccountingProviderConnectionJob('unsupported', 1);
    expect(fn () => $job->handle(Mockery::mock(QuickBooksService::class), Mockery::mock(XeroService::class), Mockery::mock(SageService::class)))
        ->toThrow(InvalidArgumentException::class);
});
