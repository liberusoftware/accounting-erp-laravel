<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpHttp\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Liberu\Accounting\Erp\Jobs\SyncAccountingProviderConnectionJob;
use Liberu\Accounting\Erp\Models\QboConnection;
use Liberu\Accounting\ErpHttp\Http\Controllers\Controller;

class QboWebhookController extends Controller
{
    /**
     * Receive QBO change notifications. Verified with the HMAC-SHA256 verifier
     * token Intuit signs each payload with (header `intuit-signature`).
     */
    public function handle(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();

        if (! $this->verifySignature($rawBody, (string) $request->header('intuit-signature'))) {
            Log::warning('QBO webhook signature verification failed');

            return response()->json(['success' => false], 401);
        }

        $realmIds = collect($request->input('eventNotifications', []))
            ->pluck('realmId')
            ->filter(fn (mixed $realmId): bool => is_string($realmId) && $realmId !== '')
            ->unique()
            ->values();

        $connections = QboConnection::query()
            ->where('status', 'active')
            ->whereIn('realm_id', $realmIds->all())
            ->get();

        foreach ($connections as $connection) {
            SyncAccountingProviderConnectionJob::dispatch('qbo', (int) $connection->getKey());
        }

        Log::info('QBO webhook received', [
            'realms' => $realmIds->all(),
            'connections_queued' => $connections->count(),
        ]);

        return response()->json(['success' => true, 'connections_queued' => $connections->count()]);
    }

    private function verifySignature(string $rawBody, string $signature): bool
    {
        $token = config('services.qbo.webhook_verifier_token');

        if (empty($token) || $signature === '') {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $rawBody, (string) $token, true));

        return hash_equals($expected, $signature);
    }
}
