<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;

class ReceivableOpenItemResource extends Resource
{
    protected static ?string $model = ReceivableOpenItem::class;

    protected static ?string $navigationLabel = 'Receivables';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('reference')->required(), TextInput::make('party_id')->numeric()->required(), TextInput::make('original_amount')->numeric()->required(), TextInput::make('currency')->required()->length(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('reference')->searchable(), TextColumn::make('party.name')->label('Customer'), TextColumn::make('original_amount')->money(), TextColumn::make('status')->badge(), TextColumn::make('due_on')->date()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListReceivables::route('/')];
    }
}
