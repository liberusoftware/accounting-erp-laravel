<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
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
use Liberu\Accounting\Erp\Models\Estimate;
use Liberu\Accounting\Erp\Services\SalesOrderService;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\Pages\CreateEstimate;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\Pages\EditEstimate;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\Pages\ListEstimates;

class EstimateResource extends Resource
{
    #[\Override]
    protected static ?string $model = Estimate::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    #[\Override]
    protected static ?int $navigationSort = 2;

    #[\Override]
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'estimate_number';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'customer_name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('estimate_number')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record): bool => $record !== null),

                DatePicker::make('estimate_date')
                    ->required()
                    ->default(now()),

                DatePicker::make('expiration_date')
                    ->minDate(fn ($get) => $get('estimate_date'))
                    ->default(now()->addDays(30)),

                Select::make('tax_rate_id')
                    ->relationship('taxRate', 'name')
                    ->live(),

                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        TextInput::make('description')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live(),
                        TextInput::make('unit_price')
                            ->numeric()
                            ->required()
                            ->live(),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->collapsible(),

                TextInput::make('subtotal_amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('tax_amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('total_amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'viewed' => 'Viewed',
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                        'expired' => 'Expired',
                    ])
                    ->default('draft')
                    ->required(),

                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('terms')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('estimate_number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('estimate_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('expiration_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'sent',
                        'primary' => 'viewed',
                        'success' => 'accepted',
                        'danger' => 'declined',
                        'warning' => 'expired',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'viewed' => 'Viewed',
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                        'expired' => 'Expired',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                // The two convert paths are mutually exclusive: each hides once the
                // estimate has gone down the other, so one estimate can't yield two invoices.
                Action::make('convert_to_invoice')
                    ->label('Convert to Invoice')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->visible(fn ($record): bool => $record->status === 'accepted' && ! $record->invoice_id && ! $record->salesOrder()->exists())
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->convertToInvoice()),
                Action::make('convertToSalesOrder')
                    ->label('Convert to Sales Order')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->requiresConfirmation()
                    ->visible(fn (Estimate $record): bool => $record->status === 'accepted' && ! $record->salesOrder()->exists() && ! $record->invoice_id)
                    ->action(function (Estimate $record): void {
                        try {
                            app(SalesOrderService::class)->createFromEstimate($record);
                            Notification::make()->title('Sales order created')->success()->send();
                        } catch (\DomainException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        } catch (\Throwable) {
                            Notification::make()->title('Could not create the sales order. Please retry.')->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('estimate_date', 'desc');
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListEstimates::route('/'),
            'create' => CreateEstimate::route('/create'),
            'edit' => EditEstimate::route('/{record}/edit'),
        ];
    }
}
