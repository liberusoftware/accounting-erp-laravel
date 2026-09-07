<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final class FindDuplicateParties
{
    /** @return Collection<int, \Illuminate\Database\Eloquent\Collection<int, Party>> */
    public function handle(int $legalEntityId, ?string $type = null): Collection
    {
        $parties = Party::query()
            ->where('legal_entity_id', $legalEntityId)
            ->when($type !== null, fn ($query) => $query->where('type', $type))
            ->where('status', '!=', 'archived')
            ->orderBy('name')
            ->get();

        return collect($parties->groupBy(function (Party $party): string {
            $email = mb_strtolower(trim((string) $party->email));
            $name = mb_strtolower(trim($party->name));

            return $email !== '' ? 'email:'.$email : 'name:'.$name;
        })->filter(static fn (Collection $group): bool => $group->count() > 1)->values()->all());
    }
}
