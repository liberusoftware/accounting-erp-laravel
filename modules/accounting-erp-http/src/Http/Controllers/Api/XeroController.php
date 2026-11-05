<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpHttp\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Liberu\Accounting\Erp\Models\XeroConnection;
use Liberu\Accounting\Erp\Services\XeroService;
use Liberu\Accounting\ErpHttp\Http\Controllers\Controller;

class XeroController extends Controller
{
    public function __construct(private readonly XeroService $xero) {}

    public function connect(Request $request): JsonResponse
    {
        $state = Str::random(40);
        Cache::put($this->stateCacheKey($request), $state, now()->addMinutes(10));

        return response()->json([
            'authorization_url' => $this->xero->getAuthorizationUrl($state),
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        $validated = $request->validate(['code' => ['required', 'string']]);

        // Verify OAuth state (CSRF / auth-code injection). Stashed in the cache
        // under the authenticated user by connect() — api routes have no session.
        $expected = Cache::pull($this->stateCacheKey($request));
        abort_if($expected === null || ! hash_equals($expected, (string) $request->input('state')), 403, 'Invalid OAuth state');

        $connection = $this->xero->handleCallback((int) $request->user()->id, $validated['code']);

        // Connections are team-shared: stamp the acting team (no creating hook does it).
        $connection->team_id = $request->user()->current_team_id;
        $connection->save();

        return response()->json(['success' => true, 'tenant_id' => $connection->tenant_id]);
    }

    public function sync(Request $request, XeroConnection $connection): JsonResponse
    {
        abort_unless($connection->team_id === ($request->user()->current_team_id ?? -1), 403);

        $counts = $this->xero->sync($connection);

        return response()->json([
            'success' => true,
            'customers_synced' => $counts['customers'],
            'vendors_synced' => $counts['vendors'],
            'invoices_synced' => $counts['invoices'],
            'accounts_synced' => $counts['accounts'],
            'bills_synced' => $counts['bills'],
            'payments_synced' => $counts['payments'],
            'estimates_synced' => $counts['estimates'],
            'credit_memos_synced' => $counts['credit_memos'],
            'transactions_synced' => $counts['transactions'],
        ]);
    }

    public function listConnections(Request $request): JsonResponse
    {
        return response()->json([
            'connections' => XeroConnection::where('team_id', (int) ($request->user()->current_team_id ?? -1))
                ->get(['id', 'tenant_id', 'status', 'last_synced_at']),
        ]);
    }

    public function removeConnection(Request $request, XeroConnection $connection): JsonResponse
    {
        abort_unless($connection->team_id === ($request->user()->current_team_id ?? -1), 403);

        $connection->delete();

        return response()->json(['success' => true]);
    }

    private function stateCacheKey(Request $request): string
    {
        return 'xero_oauth_state:'.$request->user()->id;
    }
}
