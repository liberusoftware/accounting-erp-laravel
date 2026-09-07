<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEndFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\YearEnd\Models\YearEndClose;

final class YearEndCloseResource extends Resource
{
    protected static ?string $model = YearEndClose::class;

    protected static ?string $navigationLabel = 'Year-end closes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('fiscal_year')->numeric()->required(), TextInput::make('period_end')->required(), TextInput::make('retained_earnings_account_ref')->required(), TextInput::make('net_income')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('fiscal_year')->sortable(), TextColumn::make('period_end')->date(), TextColumn::make('retained_earnings_account_ref'), TextColumn::make('net_income'), TextColumn::make('status')->badge(), TextColumn::make('closed_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListYearEndCloses::route('/')];
    }
}
