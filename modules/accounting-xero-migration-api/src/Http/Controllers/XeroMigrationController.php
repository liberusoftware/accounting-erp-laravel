<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigrationApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\XeroMigration\Actions\ConnectXeroTenant;
use Liberu\Accounting\XeroMigration\Actions\RecordMigration;
use Liberu\Accounting\XeroMigration\Models\XeroConnection;

final class XeroMigrationController extends Controller
{
    public function connections(Request $request): mixed
    {
        return XeroConnection::query()->where('team_id', $this->teamId($request))->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function connect(Request $request, ConnectXeroTenant $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['tenant_ref' => 'required|string|max:160', 'access_token' => 'required|string', 'refresh_token' => 'nullable|string', 'token_expires_at' => 'nullable|date', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function record(Request $request, string $connection, RecordMigration $action): mixed
    {
        $model = XeroConnection::query()->where('team_id', $this->teamId($request))->findOrFail($connection);

        return response()->json($action->handle($model, $request->validate(['source_type' => 'required|string|max:64', 'source_id' => 'required|string|max:160', 'target_type' => 'nullable|string|max:160', 'target_id' => 'nullable|string|max:160', 'status' => 'nullable|in:pending,migrated,failed', 'error' => 'nullable|string', 'payload' => 'nullable|array'])), 201);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}
