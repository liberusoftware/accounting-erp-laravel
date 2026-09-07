<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class SupplierBillResource extends Resource
{
    protected static ?string $model = SupplierBill::class;

    protected static ?string $navigationLabel = 'Supplier Bills';

    protected static ?string $recordTitleAttribute = 'bill_number';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('party_id')->numeric()->required(), TextInput::make('bill_number')->maxLength(80),
            DatePicker::make('bill_date')->required(), DatePicker::make('due_on'), TextInput::make('currency')->required()->length(3),
            TextInput::make('capture_source'), TextInput::make('purchase_order_reference'), TextInput::make('reference_number'),
            Repeater::make('lines')->relationship()->schema([
                TextInput::make('account_code'), TextInput::make('description')->required(), TextInput::make('quantity')->numeric()->required(), TextInput::make('unit_price')->numeric()->required(), TextInput::make('discount_rate')->numeric()->default(0), TextInput::make('tax_rate')->numeric()->default(0),
            ])->columns(3)->minItems(1)->defaultItems(1),
            Select::make('status')->options(['draft' => 'Draft', 'approved' => 'Approved', 'posted' => 'Posted', 'rejected' => 'Rejected', 'void' => 'Void'])->disabled(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('bill_number')->searchable()->sortable(), TextColumn::make('party.name')->label('Supplier')->searchable(), TextColumn::make('bill_date')->date()->sortable(), TextColumn::make('due_on')->date()->sortable(), TextColumn::make('total')->money()->sortable(), TextColumn::make('payment_status')->badge(), TextColumn::make('status')->badge(),
        ])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'approved' => 'Approved', 'posted' => 'Posted', 'rejected' => 'Rejected', 'void' => 'Void']), SelectFilter::make('payment_status')->options(['unpaid' => 'Unpaid', 'partial' => 'Partial', 'paid' => 'Paid'])])->defaultSort('bill_date', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSupplierBills::route('/'), 'create' => Pages\CreateSupplierBill::route('/create'), 'edit' => Pages\EditSupplierBill::route('/{record}/edit')];
    }
}
