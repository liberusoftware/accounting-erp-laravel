<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalancesFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;

final class OpeningBalanceBatchResource extends Resource
{
    protected static ?string $model = OpeningBalanceBatch::class;

    protected static ?string $navigationLabel = 'Opening Balances';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('batch_ref')->required(), TextInput::make('migration_date')->required(), TextInput::make('currency')->length(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('batch_ref')->searchable(), TextColumn::make('migration_date')->date()->sortable(), TextColumn::make('currency'), TextColumn::make('status')->badge(), TextColumn::make('entries_count')->counts('entries'), TextColumn::make('approved_at')->dateTime()])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'validated' => 'Validated', 'approved' => 'Approved', 'reconciled' => 'Reconciled', 'failed' => 'Failed'])])->defaultSort('migration_date', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListOpeningBalanceBatches::route('/')];
    }
}
