<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;

final class CashCodingBatchResource extends Resource
{
    protected static ?string $model = CashCodingBatch::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Cash Coding';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('reference')->required()->maxLength(180), TextInput::make('currency')->required()->length(3), TextInput::make('payee_creation_policy')->default('never'), Textarea::make('lines')->required()->helperText('JSON list of source_reference, amount, currency, and account_id values.')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('reference')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('total_amount')->numeric(), TextColumn::make('currency'), TextColumn::make('posted_at')->dateTime()])->defaultSort('created_at', 'desc');
    }
}
