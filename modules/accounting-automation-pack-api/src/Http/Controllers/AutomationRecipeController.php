<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPackApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\AutomationPack\Actions\CreateAutomationRecipe;
use Liberu\Accounting\AutomationPack\Actions\SimulateAutomationRecipe;
use Liberu\Accounting\AutomationPack\Models\AutomationRecipe;

final class AutomationRecipeController extends Controller
{
    public function index(Request $request): mixed
    {
        return response()->json(['data' => AutomationRecipe::query()->where('team_id', $this->teamId($request))->latest()->paginate($request->integer('per_page', 25))]);
    }

    public function store(Request $request, CreateAutomationRecipe $action): mixed
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'trigger' => ['required', 'string', 'max:120'], 'action' => ['required', 'string', 'max:120'], 'schedule' => ['nullable', 'string', 'max:120'], 'idempotency_key' => ['nullable', 'string', 'max:160'], 'configuration' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId($request)])], 201);
    }

    public function simulate(Request $request, string $recipe, SimulateAutomationRecipe $action): mixed
    {
        $record = AutomationRecipe::query()->where('team_id', $this->teamId($request))->findOrFail($recipe);

        return response()->json(['data' => $action->handle($record, $request->validate(['payload' => ['nullable', 'array']])['payload'] ?? [])]);
    }

    private function teamId(Request $request): int
    {
        abort_if(($id = $request->user()?->current_team_id) === null, 403, 'A team context is required.');

        return (int) $id;
    }
}
