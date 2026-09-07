<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepreciationFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Depreciation\Models\DepreciationSchedule;
use Liberu\Accounting\DepreciationFilament\Resources\DepreciationScheduleResource\Pages\ListDepreciationSchedules;

final class DepreciationScheduleResource extends Resource
{
    protected static ?string $model = DepreciationSchedule::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('asset_ref')->required(), TextInput::make('book_ref')->required(), TextInput::make('method')->required(), TextInput::make('useful_life_months')->numeric()->required(), TextInput::make('cost')->numeric()->required(), TextInput::make('residual_value')->numeric()->default(0), TextInput::make('start_date')->type('date')->required(), TextInput::make('currency')->required()->maxLength(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('asset_ref')->searchable(), TextColumn::make('book_ref'), TextColumn::make('method'), TextColumn::make('cost')->money(), TextColumn::make('status')->badge()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDepreciationSchedules::route('/')];
    }
}
