<?php

declare(strict_types=1);

use Liberu\Accounting\ClientCollaborationFilament\ClientCollaborationFilamentPlugin;

it('exposes the client collaboration Filament plugin', function (): void {
    expect(app(ClientCollaborationFilamentPlugin::class)->getId())
        ->toBe('module-accounting-client-collaboration-filament');
});
