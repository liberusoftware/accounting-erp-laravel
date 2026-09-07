<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;

final class PayableOpenItemResource extends Resource
{
    protected static ?string $model = PayableOpenItem::class;

    protected static ?string $navigationLabel = 'Payables';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference')->required(), TextInput::make('party_id')->numeric()->required(),
            DatePicker::make('issued_on')->required(), DatePicker::make('due_on'),
            TextInput::make('original_amount')->numeric()->required(), TextInput::make('currency')->required()->length(3),
            TextInput::make('payment_terms'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('reference')->searchable(), TextColumn::make('party.name')->label('Supplier'),
            TextColumn::make('original_amount')->money(), TextColumn::make('status')->badge(), TextColumn::make('due_on')->date(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPayables::route('/'), 'create' => Pages\CreatePayable::route('/create')];
    }
}
