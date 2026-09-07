<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\Vat\Models\VatRecord;

final class VatRecordResource extends Resource
{
    protected static ?string $model = VatRecord::class;

    protected static ?string $navigationLabel = 'VAT records';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('direction')->required(), TextInput::make('tax_code')->required(), TextInput::make('net_amount')->numeric()->required(), TextInput::make('tax_amount')->numeric()->required(), TextInput::make('tax_rate')->numeric(), Toggle::make('reverse_charge'), TextInput::make('scheme')->default('standard'), TextInput::make('box')->numeric(), DatePicker::make('occurred_on')->required(), TextInput::make('source_type'), TextInput::make('source_id')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('direction')->badge(), TextColumn::make('tax_code'), TextColumn::make('net_amount'), TextColumn::make('tax_amount'), TextColumn::make('scheme'), TextColumn::make('box'), TextColumn::make('occurred_on')->date(), TextColumn::make('status')->badge()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListVatRecords::route('/')];
    }
}
