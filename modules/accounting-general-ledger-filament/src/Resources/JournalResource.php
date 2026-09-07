<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\GeneralLedger\Actions\PostJournal;
use Liberu\Accounting\GeneralLedger\Actions\ReverseJournal;
use Liberu\Accounting\GeneralLedger\Enums\JournalStatus;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;
use Liberu\Accounting\GeneralLedgerFilament\Resources\JournalResource\Pages\CreateJournal;
use Liberu\Accounting\GeneralLedgerFilament\Resources\JournalResource\Pages\ListJournals;

final class JournalResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('book_id')->numeric()->required(), DatePicker::make('entry_date')->required(), TextInput::make('description'), Repeater::make('lines')->schema([TextInput::make('account_id')->numeric()->required(), TextInput::make('debit')->numeric()->default(0), TextInput::make('credit')->numeric()->default(0), TextInput::make('description')])->minItems(2)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('entry_number'), TextColumn::make('entry_date')->date(), TextColumn::make('journal_type'), TextColumn::make('status')->badge()])->recordActions([Action::make('post')->requiresConfirmation()->visible(fn (JournalEntry $record): bool => $record->status === JournalStatus::Draft)->action(fn (JournalEntry $record): JournalEntry => app(PostJournal::class)->handle($record)), Action::make('reverse')->requiresConfirmation()->visible(fn (JournalEntry $record): bool => $record->status === JournalStatus::Posted)->action(fn (JournalEntry $record): JournalEntry => app(ReverseJournal::class)->handle($record))]);
    }

    public static function getPages(): array
    {
        return ['index' => ListJournals::route('/'), 'create' => CreateJournal::route('/create')];
    }
}
