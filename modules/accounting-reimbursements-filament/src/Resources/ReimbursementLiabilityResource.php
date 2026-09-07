<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\Reimbursements\Models\ReimbursementLiability;

final class ReimbursementLiabilityResource extends Resource
{
    protected static ?string $model = ReimbursementLiability::class;

    protected static ?string $navigationLabel = 'Reimbursements';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('payee_ref')->required(), TextInput::make('kind'), TextInput::make('currency')->required()->length(3), TextInput::make('amount')->numeric()->required(), TextInput::make('source_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('payee_ref')->searchable(), TextColumn::make('kind'), TextColumn::make('currency'), TextColumn::make('amount'), TextColumn::make('status')->badge(), TextColumn::make('batch_id')->sortable()])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListReimbursementLiabilities::route('/')];
    }
}
