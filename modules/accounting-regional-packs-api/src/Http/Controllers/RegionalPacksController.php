<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacksApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\RegionalPacks\Actions\CreateRegionalPack;
use Liberu\Accounting\RegionalPacks\Actions\PublishRegionalPack;
use Liberu\Accounting\RegionalPacks\Actions\RunComplianceTests;
use Liberu\Accounting\RegionalPacks\Models\RegionalPack;

final class RegionalPacksController extends Controller
{
    public function index(): mixed
    {
        return RegionalPack::query()->with('artifacts')->latest()->paginate(25);
    }

    public function store(Request $request, CreateRegionalPack $action): RegionalPack
    {
        return $action->handle($request->validate(['country_code' => 'required|string|size:2', 'locale' => 'required|string|max:20', 'currency' => 'required|string|size:3', 'version' => 'nullable|string|max:40', 'effective_from' => 'nullable|date', 'metadata' => 'nullable|array']));
    }

    public function show(RegionalPack $pack): RegionalPack
    {
        return $pack->load('artifacts');
    }

    public function publish(Request $request, RegionalPack $pack, PublishRegionalPack $action): RegionalPack
    {
        return $action->handle($pack, $request->validate(['artifacts' => 'required|array'])['artifacts']);
    }

    public function test(RegionalPack $pack, RunComplianceTests $action): RegionalPack
    {
        return $action->handle($pack);
    }
}
