<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\PurchaseOrders;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\Erp\Models\PurchaseOrder;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\PurchaseOrders\Pages\ListPurchaseOrders;

class PurchaseOrderResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Purchasing';

    #[\Override]
    protected static ?string $model = PurchaseOrder::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->relationship('supplier', 'supplier_first_name')
                    ->required(),
                TextInput::make('po_number')
                    ->default(fn (): string => PurchaseOrder::generatePoNumber())
                    ->disabled()
                    ->required(),
                DatePicker::make('order_date')
                    ->required(),
                DatePicker::make('expected_delivery_date'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
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
                TextColumn::make('po_number')
                    ->searchable(),
                TextColumn::make('supplier.supplier_first_name')
                    ->searchable(),
                TextColumn::make('order_date')
                    ->date(),
                TextColumn::make('total_amount')
                    ->money(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'primary' => 'sent',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status'),
            ])
            ->recordActions([
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
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
