<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ChartOfAccounts\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Services\AccountCsvService;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ChartOfAccounts\ChartOfAccountsResource;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListChartOfAccounts extends ListRecords
{
    #[\Override]
    protected static string $resource = ChartOfAccountsResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->provisionAction(),
            $this->exportAction(),
            $this->importAction(),
        ];
    }

    private function provisionAction(): Action
    {
        return Action::make('provisionChart')
            ->label('Seed standard chart')
            ->icon('heroicon-o-sparkles')
            ->color('gray')
            ->requiresConfirmation()
            ->modalDescription('Create a standard chart of accounts for this team. Skipped if accounts already exist.')
            ->action(function (TenantProvisioningService $service): void {
                $tenant = Filament::getTenant();
                if (! $tenant instanceof Team) {
                    return;
                }
                $count = $service->provisionChartOfAccounts($tenant);
                Notification::make()
                    ->title($count > 0 ? "Provisioned {$count} accounts." : 'Chart already exists; nothing added.')
                    ->success()
                    ->send();
            });
    }

    private function exportAction(): Action
    {
        return Action::make('exportAccounts')
            ->label('Export CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function (AccountCsvService $service): StreamedResponse {
                $csv = $service->export();

                return response()->streamDownload(
                    fn () => print ($csv),
                    'chart-of-accounts.csv',
                    ['Content-Type' => 'text/csv'],
                );
            });
    }

    private function importAction(): Action
    {
        return Action::make('importAccounts')
            ->label('Import CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            ->schema([
                FileUpload::make('file')
                    ->label('CSV file')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                    ->maxSize(5120)
                    ->required()
                    ->helperText('Columns: account_number, account_name, account_type, normal_balance, opening_balance, parent_number, description, is_active'),
            ])
            ->action(function (array $data, AccountCsvService $service): void {
                // $data['file'] is the client-supplied Livewire upload path; reject
                // traversal and read via the disk (confined to storage/app/public)
                // so a crafted value like ../../.env can't read arbitrary files.
                abort_if(str_contains((string) $data['file'], '..'), 403);
                $result = $service->import((string) Storage::disk('public')->get($data['file']));

                $notification = Notification::make()
                    ->title("Imported: {$result['created']} created, {$result['updated']} updated");

                if ($result['errors'] !== []) {
                    $notification->body(implode("\n", array_slice($result['errors'], 0, 10)))->warning();
                } else {
                    $notification->success();
                }

                $notification->send();
            });
    }
}
