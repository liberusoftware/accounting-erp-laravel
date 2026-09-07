<?php

use Liberu\Accounting\ProjectsAndJobsFilament\ProjectsAndJobsFilamentPlugin;

it('discovers the projects and jobs Filament plugin on the app panel', function (): void {
    expect(app(ProjectsAndJobsFilamentPlugin::class)->getId())->toBe('module-accounting-projects-and-jobs-filament');
});
