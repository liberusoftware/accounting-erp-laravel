<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\FixedAssets\Actions\AcquireAsset;
use Liberu\Accounting\FixedAssets\Actions\AddAssetComponent;
use Liberu\Accounting\FixedAssets\Actions\AddAssetDocument;
use Liberu\Accounting\FixedAssets\Actions\ArchiveAsset;
use Liberu\Accounting\FixedAssets\Actions\AssignAssetCustodian;
use Liberu\Accounting\FixedAssets\Actions\AssignAssetLocation;
use Liberu\Accounting\FixedAssets\Actions\CapitalizeAsset;
use Liberu\Accounting\FixedAssets\Actions\CreateCategory;
use Liberu\Accounting\FixedAssets\Actions\CreateCustodian;
use Liberu\Accounting\FixedAssets\Actions\CreateLocation;
use Liberu\Accounting\FixedAssets\Actions\DisposeAsset;
use Liberu\Accounting\FixedAssets\Actions\UpdateAsset;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Models\AssetCategory;
use Liberu\Accounting\FixedAssets\Models\AssetCustodian;
use Liberu\Accounting\FixedAssets\Models\AssetLocation;
use Liberu\Accounting\FixedAssets\Queries\AssetQuery;
use Liberu\Accounting\FixedAssetsApi\Http\Resources\AssetResource;

final class FixedAssetsController extends Controller
{
    public function __construct(private readonly AssetQuery $query) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $paginator = $this->query->paginate($this->teamId($request), $this->status($request), $request->integer('page.size', 25));

        return AssetResource::collection($paginator);
    }

    public function show(Request $request, Asset $asset): AssetResource
    {
        $this->authorizeView($request, $asset);

        return new AssetResource($asset->load(['category', 'components', 'books', 'documents', 'location', 'custodian']));
    }

    public function store(Request $request, AcquireAsset $action): AssetResource
    {
        $attributes = $request->validate([
            'category_id' => ['required', 'integer', 'exists:accounting_fixed_asset_categories,id'],
            'asset_ref' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'acquired_on' => ['required', 'date'],
            'cost' => ['required', 'numeric', 'gt:0'],
            'salvage_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'location_ref' => ['nullable', 'string', 'max:255'],
            'custodian_ref' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);
        $teamId = $this->teamId($request);
        $category = AssetCategory::query()->whereKey($attributes['category_id'])->where('team_id', $teamId)->firstOrFail();
        Gate::authorize('create', Asset::class);

        return new AssetResource($action->handle($category, [...$attributes, 'team_id' => $teamId])->load('category'));
    }

    public function category(Request $request, CreateCategory $action): JsonResponse
    {
        $attributes = $request->validate([
            'category_ref' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'asset_account_ref' => ['required', 'string', 'max:255'],
            'depreciation_account_ref' => ['required', 'string', 'max:255'],
            'useful_life_months' => ['required', 'integer', 'min:1'],
            'depreciation_method' => ['required', 'string', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ]);
        $teamId = $this->teamId($request);
        Gate::authorize('create', Asset::class);

        $category = $action->handle([...$attributes, 'team_id' => $teamId]);

        return response()->json(['data' => [
            'id' => (string) $category->getKey(),
            'type' => 'accounting-fixed-asset-categories',
            'attributes' => [
                'category_ref' => $category->category_ref,
                'name' => $category->name,
                'asset_account_ref' => $category->asset_account_ref,
                'depreciation_account_ref' => $category->depreciation_account_ref,
                'useful_life_months' => $category->useful_life_months,
                'depreciation_method' => $category->depreciation_method,
            ],
        ]], 201);
    }

    public function location(Request $request, CreateLocation $action): JsonResponse
    {
        $attributes = $request->validate(['location_ref' => ['required', 'string', 'max:100'], 'name' => ['required', 'string', 'max:255'], 'metadata' => ['nullable', 'array']]);
        Gate::authorize('create', Asset::class);
        $location = $action->handle([...$attributes, 'team_id' => $this->teamId($request)]);

        return response()->json(['data' => ['id' => (string) $location->getKey(), 'location_ref' => $location->location_ref, 'name' => $location->name]], 201);
    }

    public function custodian(Request $request, CreateCustodian $action): JsonResponse
    {
        $attributes = $request->validate(['custodian_ref' => ['required', 'string', 'max:100'], 'name' => ['required', 'string', 'max:255'], 'email' => ['nullable', 'email', 'max:255'], 'metadata' => ['nullable', 'array']]);
        Gate::authorize('create', Asset::class);
        $custodian = $action->handle([...$attributes, 'team_id' => $this->teamId($request)]);

        return response()->json(['data' => ['id' => (string) $custodian->getKey(), 'custodian_ref' => $custodian->custodian_ref, 'name' => $custodian->name, 'email' => $custodian->email]], 201);
    }

    public function update(Request $request, Asset $asset, UpdateAsset $action): AssetResource
    {
        $this->authorizeUpdate($request, $asset);
        $attributes = $request->validate([
            'asset_ref' => ['sometimes', 'string', 'max:100'],
            'name' => ['sometimes', 'string', 'max:255'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ]);

        return new AssetResource($action->handle($asset, $attributes));
    }

    public function delete(Request $request, Asset $asset, ArchiveAsset $action): JsonResponse
    {
        $this->authorizeUpdate($request, $asset);
        $action->handle($asset);

        return response()->json(status: 204);
    }

    public function capitalize(Request $request, Asset $asset, CapitalizeAsset $action): AssetResource
    {
        $this->authorizeUpdate($request, $asset);
        $bookReference = $request->validate(['book_ref' => ['required', 'string', 'max:100']])['book_ref'];

        return new AssetResource($action->handle($asset, $bookReference));
    }

    public function dispose(Request $request, Asset $asset, DisposeAsset $action): AssetResource
    {
        $this->authorizeUpdate($request, $asset);
        $reason = $request->validate(['reason' => ['required', 'string', 'max:5000']])['reason'];

        return new AssetResource($action->handle($asset, $reason));
    }

    public function document(Request $request, Asset $asset, AddAssetDocument $action): AssetResource
    {
        $this->authorizeUpdate($request, $asset);
        $attributes = $request->validate([
            'document_ref' => ['required', 'string', 'max:100'],
            'kind' => ['required', 'string', 'max:100'],
            'file_ref' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'checksum' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);
        $action->handle($asset, [...$attributes, 'attached_by' => (string) $request->user()->getAuthIdentifier()]);

        return new AssetResource($asset->load('documents'));
    }

    public function component(Request $request, Asset $asset, AddAssetComponent $action): AssetResource
    {
        $this->authorizeUpdate($request, $asset);
        $attributes = $request->validate([
            'component_ref' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'gt:0'],
            'useful_life_months' => ['required', 'integer', 'min:1'],
            'metadata' => ['nullable', 'array'],
        ]);
        $action->handle($asset, $attributes);

        return new AssetResource($asset->load(['components', 'books']));
    }

    public function assignLocation(Request $request, Asset $asset, AssignAssetLocation $action): AssetResource
    {
        $this->authorizeUpdate($request, $asset);
        $id = $request->validate(['location_id' => ['required', 'integer']])['location_id'];
        $location = AssetLocation::query()->whereKey($id)->where('team_id', $this->teamId($request))->firstOrFail();

        return new AssetResource($action->handle($asset, $location));
    }

    public function assignCustodian(Request $request, Asset $asset, AssignAssetCustodian $action): AssetResource
    {
        $this->authorizeUpdate($request, $asset);
        $id = $request->validate(['custodian_id' => ['required', 'integer']])['custodian_id'];
        $custodian = AssetCustodian::query()->whereKey($id)->where('team_id', $this->teamId($request))->firstOrFail();

        return new AssetResource($action->handle($asset, $custodian));
    }

    public function summary(Request $request, Asset $asset): JsonResponse
    {
        $this->authorizeView($request, $asset);

        return response()->json(['data' => $this->query->registerSummary($asset->load(['books', 'documents']))]);
    }

    private function status(Request $request): ?AssetStatus
    {
        if (! $request->filled('status')) {
            return null;
        }
        $status = AssetStatus::tryFrom($request->string('status')->toString());
        abort_if($status === null, 422, 'The selected status is invalid.');

        return $status;
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function authorizeView(Request $request, Asset $asset): void
    {
        abort_unless($asset->team_id === $this->teamId($request), 404);
        Gate::authorize('view', $asset);
    }

    private function authorizeUpdate(Request $request, Asset $asset): void
    {
        abort_unless($asset->team_id === $this->teamId($request), 404);
        Gate::authorize('update', $asset);
    }
}
