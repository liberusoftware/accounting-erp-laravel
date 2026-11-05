<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpHttp\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Liberu\Accounting\Erp\Models\QboConnection;
use Liberu\Accounting\Erp\Services\QuickBooksService;
use Liberu\Accounting\ErpHttp\Http\Controllers\Controller;

class QboController extends Controller
{
    public function __construct(private readonly QuickBooksService $qbo) {}

    /**
     * Acting user's current team, or -1 when there is none — a sentinel that
     * matches no row (team ids are positive), so a tenantless caller gets an
     * empty result / 403 rather than leaking unassigned (team_id IS NULL) rows.
     */
    private function currentTeamId(): int
    {
        return (int) (auth()->user()->current_team_id ?? -1);
    }

    private function stateCacheKey(Request $request): string
    {
        return 'qbo_oauth_state:'.$request->user()->id;
    }

    /**
     * Begin the OAuth 2.0 flow — return the Intuit authorization URL for the client to open.
     */
    public function connect(Request $request): JsonResponse
    {
        $state = Str::random(40);
        Cache::put($this->stateCacheKey($request), $state, now()->addMinutes(10));

        return response()->json([
            'authorization_url' => $this->qbo->getAuthorizationUrl($state),
        ]);
    }

    /**
     * OAuth callback — exchange the code for tokens and store the connection.
     */
    public function callback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'realmId' => ['required', 'string'],
        ]);

        // Verify OAuth state (CSRF / auth-code injection). Stashed in the cache
        // under the authenticated user by connect() — api routes have no session.
        $expected = Cache::pull($this->stateCacheKey($request));
        abort_if($expected === null || ! hash_equals($expected, (string) $request->input('state')), 403, 'Invalid OAuth state');

        $connection = $this->qbo->handleCallback(
            (int) $request->user()->id,
            $validated['code'],
            $validated['realmId'],
        );

        // Connections are team-shared: stamp the acting team (no creating hook does it).
        $connection->team_id = $request->user()->current_team_id;
        $connection->save();

        return response()->json([
            'success' => true,
            'realm_id' => $connection->realm_id,
        ]);
    }

    /**
     * @return JsonResponse list of the authenticated user's QBO connections
     */
    public function listConnections(Request $request): JsonResponse
    {
        return response()->json([
            'connections' => QboConnection::where('team_id', $this->currentTeamId())
                ->get(['id', 'realm_id', 'status', 'last_synced_at']),
        ]);
    }

    /**
     * Pull invoices from QBO into the local ledger.
     */
    public function sync(Request $request, QboConnection $connection): JsonResponse
    {
        abort_unless($connection->team_id === $this->currentTeamId(), 403);

        $counts = $this->qbo->sync($connection);

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

    public function removeConnection(Request $request, QboConnection $connection): JsonResponse
    {
        abort_unless($connection->team_id === $this->currentTeamId(), 403);

        $connection->delete();

        return response()->json(['success' => true]);
    }
}
