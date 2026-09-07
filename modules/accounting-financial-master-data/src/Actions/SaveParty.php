<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialMasterData\Events\MasterRecordCreated;
use Liberu\Accounting\FinancialMasterData\Exceptions\DuplicateMasterRecord;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class SaveParty
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array<string, mixed> $attributes */
    public function handle(array $attributes, ?Party $party = null): Party
    {
        return DB::transaction(function () use ($attributes, $party): Party {
            $legalEntityId = $attributes['legal_entity_id'] ?? $party?->legal_entity_id;
            $type = $attributes['type'] ?? $party?->type?->value;
            if (empty($legalEntityId) || empty($type) || blank($attributes['name'] ?? $party?->name)) {
                throw new DuplicateMasterRecord('Legal entity, party type, and name are required.');
            }
            $query = Party::query()->where('legal_entity_id', $legalEntityId)
                ->where('type', $type)
                ->where('status', '!=', 'archived')
                ->where(function ($match) use ($attributes, $party): void {
                    $match->whereRaw('lower(name) = ?', [mb_strtolower(trim((string) ($attributes['name'] ?? $party?->name)))]);
                    if (! empty($attributes['email'] ?? $party?->email)) {
                        $match->orWhereRaw('lower(email) = ?', [mb_strtolower(trim((string) ($attributes['email'] ?? $party?->email)))]);
                    }
                });
            if ($party?->exists) {
                $query->where($party->getKeyName(), '!=', $party->getKey());
            }
            if ($query->exists()) {
                throw new DuplicateMasterRecord('A matching customer or supplier already exists for this legal entity.');
            }

            $party ??= new Party();
            $created = ! $party->exists;
            $party->fill($attributes);
            $party->save();
            if ($created) {
                $this->events->dispatch(new MasterRecordCreated($party));
            }

            return $party->refresh();
        });
    }
}
