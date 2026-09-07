<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\FinancialMasterData\Actions\SaveReferenceData;
use Liberu\Accounting\FinancialMasterData\Exceptions\DuplicateMasterRecord;
use Liberu\Accounting\FinancialMasterData\Models\Address;
use Liberu\Accounting\FinancialMasterData\Models\BankDetailReference;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\FinancialMasterDataApi\Http\Requests\StorePartyDetailRequest;
use Liberu\Accounting\FinancialMasterDataApi\Http\Resources\PartyDetailResource;

final class PartyDetailController extends Controller
{
    public function index(string $party, string $detail): JsonResponse
    {
        $model = $this->party($party);
        $class = $this->class($detail);
        abort_unless(request()->user()?->tokenCan('accounting.master-data.read'), 403);

        return PartyDetailResource::collection($class::query()->where('party_id', $model->getKey())->latest()->get())->response();
    }

    public function store(StorePartyDetailRequest $request, string $party, string $detail, SaveReferenceData $save): JsonResponse
    {
        $model = $this->party($party);
        $class = $this->class($detail);
        abort_unless($request->user()?->tokenCan('accounting.master-data.write'), 403);
        $data = $request->validated() + ['party_id' => $model->getKey()];
        try {
            return (new PartyDetailResource($save->handle($class, $data)))->response()->setStatusCode(201);
        } catch (DuplicateMasterRecord $exception) {
            throw ValidationException::withMessages(['detail' => $exception->getMessage()]);
        }
    }

    public function destroy(string $party, string $detail, string $record): Response
    {
        $model = $this->party($party);
        $class = $this->class($detail);
        abort_unless(request()->user()?->tokenCan('accounting.master-data.write'), 403);
        $class::query()->where('party_id', $model->getKey())->findOrFail($record)->delete();

        return response()->noContent();
    }

    private function party(string $party): Party
    {
        return Party::query()->findOrFail($party);
    }

    /** @return class-string<Address|BankDetailReference> */
    private function class(string $detail): string
    {
        return match ($detail) {
            'addresses' => Address::class, 'bank-details' => BankDetailReference::class, default => abort(404)
        };
    }
}
