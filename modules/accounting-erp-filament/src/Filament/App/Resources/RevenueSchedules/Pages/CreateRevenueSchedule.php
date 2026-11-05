<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\RevenueSchedules\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Services\RevenueRecognitionService;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\RevenueSchedules\RevenueScheduleResource;

class CreateRevenueSchedule extends CreateRecord
{
    #[\Override]
    protected static string $resource = RevenueScheduleResource::class;

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(RevenueRecognitionService::class)->createFromInvoice(
                Invoice::findOrFail($data['invoice_id']),
                (int) $data['periods'],
                Account::findOrFail($data['deferred_account_id']),
                Account::findOrFail($data['revenue_account_id']),
            );
        } catch (\InvalidArgumentException $e) {
            Notification::make()
                ->title('Cannot create revenue schedule')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw new Halt();
        }
    }
}
