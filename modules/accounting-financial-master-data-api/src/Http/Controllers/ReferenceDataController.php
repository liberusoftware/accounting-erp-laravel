<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\FinancialMasterData\Actions\SaveReferenceData;
use Liberu\Accounting\FinancialMasterData\Exceptions\DuplicateMasterRecord;
use Liberu\Accounting\FinancialMasterData\Models\ItemService;
use Liberu\Accounting\FinancialMasterData\Models\PaymentTerm;
use Liberu\Accounting\FinancialMasterData\Models\TaxProfile;
use Liberu\Accounting\FinancialMasterDataApi\Http\Requests\StoreReferenceDataRequest;
use Liberu\Accounting\FinancialMasterDataApi\Http\Requests\UpdateReferenceDataRequest;
use Liberu\Accounting\FinancialMasterDataApi\Http\Resources\ReferenceDataResource;

final class ReferenceDataController extends Controller
{
    /** @return array{class: class-string<Model>, ability: string} */
    private function definition(string $resource): array
    {
        return match ($resource) {
            'items-services' => ['class' => ItemService::class, 'ability' => 'accounting.master-data.items'],
            'tax-profiles' => ['class' => TaxProfile::class, 'ability' => 'accounting.master-data.tax'],
            'payment-terms' => ['class' => PaymentTerm::class, 'ability' => 'accounting.master-data.terms'],
            default => abort(404),
        };
    }

    public function index(Request $request, string $resource): JsonResponse
    {
        $definition = $this->definition($resource);
        abort_unless($request->user()?->tokenCan('accounting.master-data.read'), 403);
        $query = $definition['class']::query()->latest()->when($request->integer('legal_entity_id'), fn ($q, $id) => $q->where('legal_entity_id', $id));

        return ReferenceDataResource::collection($query->paginate(min($request->integer('per_page', 25), 100)))->response();
    }

    public function show(string $resource, string $record): ReferenceDataResource
    {
        $definition = $this->definition($resource);
        abort_unless(request()->user()?->tokenCan('accounting.master-data.read'), 403);

        return new ReferenceDataResource($definition['class']::query()->findOrFail($record));
    }

    public function store(StoreReferenceDataRequest $request, string $resource, SaveReferenceData $save): JsonResponse
    {
        $definition = $this->definition($resource);
        abort_unless($request->user()?->tokenCan('accounting.master-data.write'), 403);
        try {
            return (new ReferenceDataResource($save->handle($definition['class'], $request->validated())))->response()->setStatusCode(201);
        } catch (DuplicateMasterRecord $exception) {
            throw ValidationException::withMessages(['code' => $exception->getMessage()]);
        }
    }

    public function update(UpdateReferenceDataRequest $request, string $resource, string $record, SaveReferenceData $save): ReferenceDataResource
    {
        $definition = $this->definition($resource);
        abort_unless($request->user()?->tokenCan('accounting.master-data.write'), 403);
        $model = $definition['class']::query()->findOrFail($record);
        try {
            return new ReferenceDataResource($save->handle($definition['class'], $request->validated(), $model));
        } catch (DuplicateMasterRecord $exception) {
            throw ValidationException::withMessages(['code' => $exception->getMessage()]);
        }
    }

    public function destroy(string $resource, string $record): Response
    {
        $definition = $this->definition($resource);
        abort_unless(request()->user()?->tokenCan('accounting.master-data.write'), 403);
        $definition['class']::query()->findOrFail($record)->update(['status' => 'inactive']);

        return response()->noContent();
    }
}
