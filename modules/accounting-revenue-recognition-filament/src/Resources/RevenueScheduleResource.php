<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;

final class RevenueScheduleResource extends Resource
{
    protected static ?string $model = RevenueSchedule::class;

    protected static ?string $navigationLabel = 'Revenue schedules';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('obligation_id')->numeric()->required(), TextInput::make('total_amount')->numeric()->required(), TextInput::make('start_date')->required(), TextInput::make('periods')->numeric()->required(), TextInput::make('deferred_account_ref')->required(), TextInput::make('revenue_account_ref')->required(), TextInput::make('status')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('obligation_id')->sortable(), TextColumn::make('total_amount'), TextColumn::make('start_date')->date()->sortable(), TextColumn::make('periods'), TextColumn::make('status')->badge(), IconColumn::make('funded')->boolean()])->defaultSort('start_date', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('obligation', fn (Builder $query): Builder => $query->where('team_id', (int) (auth()->user()?->current_team_id ?? -1)));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListRevenueSchedules::route('/')];
    }
}
