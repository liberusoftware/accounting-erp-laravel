<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;

class CreateTeam extends RegisterTenant
{
    #[\Override]
    protected string $view = 'accounting-erp-filament::filament.pages.create-team';

    #[\Override]
    protected Width|string|null $maxWidth = '2xl';

    #[\Override]
    public function mount(): void {}

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Team Name')
                ->required()
                ->maxLength(255),
        ]);
    }

    #[\Override]
    protected function handleRegistration(array $data): Model
    {
        return app(\Liberu\Accounting\Erp\Actions\Jetstream\CreateTeam::class)->create(auth()->user(), $data);
    }

    public function getBreadcrumbs(): array
    {
        return [url()->current() => 'Create Team'];
    }

    public static function getLabel(): string
    {
        return 'Create Team';
    }
}
