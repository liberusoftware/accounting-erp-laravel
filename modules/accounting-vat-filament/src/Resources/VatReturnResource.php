<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\Vat\Models\VatReturn;

final class VatReturnResource extends Resource
{
    protected static ?string $model = VatReturn::class;

    protected static ?string $navigationLabel = 'VAT returns';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([DatePicker::make('period_start')->required(), DatePicker::make('period_end')->required(), TextInput::make('scheme')->default('standard')->required(), TextInput::make('submission_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('period_start')->date(), TextColumn::make('period_end')->date(), TextColumn::make('scheme'), TextColumn::make('status')->badge(), TextColumn::make('submitted_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListVatReturns::route('/')];
    }
}
