<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\PurchaseOrders\Actions\TransitionPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;
use Liberu\Accounting\PurchaseOrdersFilament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;

final class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'Purchasing';

    protected static ?string $navigationLabel = 'Purchase orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('order_number')->label('Order')->searchable()->sortable(),
            TextColumn::make('supplier_ref')->searchable(),
            TextColumn::make('order_date')->date()->sortable(),
            TextColumn::make('expected_delivery_on')->date()->sortable(),
            TextColumn::make('total_amount')->money(fn (PurchaseOrder $record): string => $record->currency),
            TextColumn::make('status')->badge(),
        ])->recordActions([
            Action::make('transition')->label('Change status')->icon('heroicon-o-arrow-path')
                ->schema([Select::make('status')->options(collect(PurchaseOrderStatus::cases())->mapWithKeys(fn (PurchaseOrderStatus $status): array => [$status->value => ucfirst(str_replace('_', ' ', $status->value))])->all())->required()])
                ->action(fn (PurchaseOrder $record, array $data, TransitionPurchaseOrder $action): PurchaseOrder => $action->handle($record, PurchaseOrderStatus::from((string) $data['status']))),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListPurchaseOrders::route('/')];
    }
}
