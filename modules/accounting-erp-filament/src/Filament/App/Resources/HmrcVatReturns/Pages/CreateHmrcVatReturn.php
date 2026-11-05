<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\HmrcVatReturns\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\HmrcVatReturns\HmrcVatReturnResource;

class CreateHmrcVatReturn extends CreateRecord
{
    #[\Override]
    protected static string $resource = HmrcVatReturnResource::class;

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure company_id is set
        $companyId = auth()->user()->currentTeam->company_id ?? null;

        if (! $companyId) {
            throw new \Exception('No company associated with current user/team');
        }

        $data['company_id'] = $companyId;

        return $data;
    }
}
