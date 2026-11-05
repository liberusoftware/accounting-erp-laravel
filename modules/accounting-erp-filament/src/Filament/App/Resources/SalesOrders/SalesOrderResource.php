<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\Erp\Models\SalesOrder;
use Liberu\Accounting\Erp\Services\SalesOrderService;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\Pages\CreateSalesOrder;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\Pages\EditSalesOrder;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\Pages\ListSalesOrders;

class SalesOrderResource extends Resource
{
    #[\Override]
    protected static ?string $model = SalesOrder::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    #[\Override]
    protected static ?int $navigationSort = 3;

    #[\Override]
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'sales_order_number';

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

                TextInput::make('sales_order_number')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record): bool => $record !== null),

                DatePicker::make('order_date')
                    ->required()
                    ->default(now()),

                // Status is system-managed (set by conversion / cancel), not
                // hand-editable — else an invoiced order could be flipped back to
                // draft while a live invoice still points at it.
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'invoiced' => 'Invoiced',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),

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

                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sales_order_number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'confirmed',
                        'success' => 'invoiced',
                        'danger' => 'cancelled',
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
                        'confirmed' => 'Confirmed',
                        'invoiced' => 'Invoiced',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('convertToInvoice')
                    ->label('Convert to Invoice')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->requiresConfirmation()
                    ->visible(fn (SalesOrder $record): bool => ! in_array($record->status, ['invoiced', 'cancelled'], true) && ! $record->invoice()->exists())
                    ->action(function (SalesOrder $record): void {
                        try {
                            app(SalesOrderService::class)->convertToInvoice($record);
                            Notification::make()->title('Invoice created')->success()->send();
                        } catch (\DomainException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        } catch (\Throwable) {
                            // e.g. a concurrent double-click tripping the unique index.
                            Notification::make()->title('Could not create the invoice. Please retry.')->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_date', 'desc');
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
            'index' => ListSalesOrders::route('/'),
            'create' => CreateSalesOrder::route('/create'),
            'edit' => EditSalesOrder::route('/{record}/edit'),
        ];
    }
}
