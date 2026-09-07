<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagementFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\ReceiptManagement\Models\Receipt;

final class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationLabel = 'Receipts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('file_ref')->required(), TextInput::make('merchant'), TextInput::make('amount')->numeric(), TextInput::make('currency')->length(3), TextInput::make('receipt_date')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('file_ref')->searchable(), TextColumn::make('merchant')->searchable(), TextColumn::make('amount'), TextColumn::make('currency'), TextColumn::make('receipt_date')->date()->sortable(), TextColumn::make('status')->badge()])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListReceipts::route('/')];
    }
}
