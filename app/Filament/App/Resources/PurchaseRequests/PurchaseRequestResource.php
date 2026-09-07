<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PurchaseRequests;

use App\Filament\App\Resources\PurchaseRequests\Pages\CreatePurchaseRequest;
use App\Filament\App\Resources\PurchaseRequests\Pages\EditPurchaseRequest;
use App\Filament\App\Resources\PurchaseRequests\Pages\ListPurchaseRequests;
use App\Models\PurchaseRequest;
use App\Services\ProcurementService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseRequestResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Purchasing';

    #[\Override]
    protected static ?string $model = PurchaseRequest::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->relationship('supplier', 'supplier_first_name')
                    ->required(),
                DatePicker::make('request_date')
                    ->required(),
                TextInput::make('total_amount')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Select::make('approval_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->disabled()
                    ->dehydrated(false),
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        TextInput::make('description')
                            ->required(),
                        TextInput::make('quantity')
                            ->numeric()
                            ->required(),
                        TextInput::make('unit_price')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(3),
                Textarea::make('notes'),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->searchable(),
                TextColumn::make('request_date')
                    ->date(),
                TextColumn::make('total_amount')
                    ->money(),
                TextColumn::make('approval_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('approval_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('submitForApproval')
                    ->label('Submit for approval')
                    ->requiresConfirmation()
                    ->visible(fn (PurchaseRequest $record): bool => $record->approval_status !== 'approved')
                    ->action(function (PurchaseRequest $record): void {
                        try {
                            $record->submitForApproval();
                            Notification::make()->title('Submitted for approval')->success()->send();
                        } catch (\Throwable) {
                            Notification::make()->title('Could not submit. Please retry.')->danger()->send();
                        }
                    }),
                Action::make('convertToPurchaseOrder')
                    ->label('Convert to Purchase Order')
                    ->requiresConfirmation()
                    ->visible(fn (PurchaseRequest $record): bool => $record->approval_status === 'approved' && ! $record->purchaseOrder()->exists())
                    ->action(function (PurchaseRequest $record): void {
                        try {
                            app(ProcurementService::class)->createPurchaseOrderFromRequest($record);
                            Notification::make()->title('Purchase order created')->success()->send();
                        } catch (\DomainException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        } catch (\Throwable) {
                            Notification::make()->title('Could not create the purchase order. Please retry.')->danger()->send();
                        }
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseRequests::route('/'),
            'create' => CreatePurchaseRequest::route('/create'),
            'edit' => EditPurchaseRequest::route('/{record}/edit'),
        ];
    }
}
