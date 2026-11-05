<?php

namespace Liberu\Accounting\ErpHttp\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Accounting\Erp\Jobs\SyncAccountingProviderConnectionJob;
use Liberu\Accounting\Erp\Models\XeroConnection;
use Liberu\Accounting\ErpHttp\Http\Controllers\Controller;

final class XeroWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $key = (string) config('services.xero.webhook_key');
        $signature = (string) $request->header('x-xero-signature');

        abort_unless($key !== '' && $signature !== '' && hash_equals(
            base64_encode(hash_hmac('sha256', $request->getContent(), $key, true)),
            $signature,
        ), 401);

        $tenantIds = collect($request->input('events', []))->pluck('tenantId')
            ->filter(fn (mixed $id): bool => is_string($id) && $id !== '')->unique();

        $connections = XeroConnection::query()->where('status', 'active')
            ->whereIn('tenant_id', $tenantIds)->get();

        foreach ($connections as $connection) {
            SyncAccountingProviderConnectionJob::dispatch('xero', (int) $connection->getKey());
        }

        return response()->json(['success' => true, 'connections_queued' => $connections->count()]);
    }
}
