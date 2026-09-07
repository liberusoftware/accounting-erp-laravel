<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final class BillPaymentResource extends Resource
{
    protected static ?string $model = BillPaymentProposal::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Bill Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('supplier_id')->numeric()->required(), TextInput::make('bill_reference')->required(), TextInput::make('amount')->numeric()->required()->minValue(0.01), TextInput::make('currency')->required()->length(3), DatePicker::make('due_date')->required(), DatePicker::make('discount_date'), TextInput::make('discount_rate')->numeric()->minValue(0)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('bill_reference')->searchable(), TextColumn::make('supplier_id'), TextColumn::make('amount')->money(fn (BillPaymentProposal $record): string => $record->currency), TextColumn::make('due_date')->date(), TextColumn::make('status')->badge(), TextColumn::make('provider')])->defaultSort('due_date');
    }
}
