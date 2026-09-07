<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\TaxReturns\Models\TaxReturn;

final class TaxReturnResource extends Resource
{
    protected static ?string $model = TaxReturn::class;

    protected static ?string $navigationLabel = 'Tax returns';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('tax_type')->required(), TextInput::make('jurisdiction')->required(), TextInput::make('period_start')->required(), TextInput::make('period_end')->required(), TextInput::make('due_on')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('tax_type'), TextColumn::make('jurisdiction'), TextColumn::make('period_start')->date(), TextColumn::make('period_end')->date()->sortable(), TextColumn::make('status')->badge(), TextColumn::make('submitted_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListTaxReturns::route('/')];
    }
}
