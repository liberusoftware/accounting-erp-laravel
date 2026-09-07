<?php

declare(strict_types=1);

namespace Liberu\Accounting\IntercompanyFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;

final class IntercompanyTransactionResource extends Resource
{
    protected static ?string $model = IntercompanyTransaction::class;

    protected static ?string $navigationLabel = 'Intercompany';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('transaction_ref')->required(), TextInput::make('source_entity_ref')->required(), TextInput::make('target_entity_ref')->required(), TextInput::make('transaction_type')->required(), TextInput::make('description')->required(), TextInput::make('amount')->numeric()->required(), TextInput::make('currency')->required()->length(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('transaction_ref')->searchable(), TextColumn::make('source_entity_ref'), TextColumn::make('target_entity_ref'), TextColumn::make('amount'), TextColumn::make('currency'), TextColumn::make('status')->badge()->sortable(), TextColumn::make('transaction_date')->dateTime()->sortable()])->defaultSort('transaction_date', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListIntercompanyTransactions::route('/')];
    }
}
