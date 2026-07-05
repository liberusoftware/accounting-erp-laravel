<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\TeamManagementService;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Smoke: mount every parameter-free Filament panel page as a super_admin and
 * assert none 500s. Catches Filament v5 class-drift (page won't mount) and
 * model<->schema drift (list query hits a missing column). Run on MySQL to
 * surface column drift that sqlite masks.
 */
class PanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_filament_pages_render(): void
    {
        config(['accounting.enforce_2fa' => false]); // render pages, don't redirect to enrolment

        $user = User::factory()->create(['email_verified_at' => now()]);
        app(TeamManagementService::class)->createPersonalTeamForUser($user);
        $user = $user->fresh();
        Role::findOrCreate('super_admin', 'web');
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $failures = [];
        $checked = 0;

        foreach (Route::getRoutes() as $route) {
            $name = (string) $route->getName();

            if (! Str::startsWith($name, ['filament.admin.', 'filament.app.'])) {
                continue;
            }
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            // Only routes we can fully parameterise: no params, or just {tenant}.
            $params = [];
            $skip = false;
            foreach ($route->parameterNames() as $p) {
                if ($p === 'tenant') {
                    $params['tenant'] = $user->current_team_id;
                } else {
                    $skip = true; // {record} etc. — needs a real row; out of scope here
                    break;
                }
            }
            if ($skip) {
                continue;
            }

            $checked++;

            try {
                $url = route($name, $params);
                $response = $this->get($url);

                if ($response->status() >= 500) {
                    $msg = $response->exception?->getMessage() ?? 'HTTP '.$response->status();
                    $failures[] = "{$name} -> ".Str::limit($msg, 160);
                }
            } catch (\Throwable $e) {
                $failures[] = "{$name} -> EXC ".Str::limit($e->getMessage(), 160);
            }
        }

        fwrite(STDERR, "\n[panel-smoke] checked {$checked} pages, ".count($failures)." failed\n");
        foreach ($failures as $f) {
            fwrite(STDERR, "  ✗ {$f}\n");
        }

        $this->assertSame([], $failures, count($failures).' Filament page(s) failed to render');
    }

    public function test_filament_edit_and_view_pages_render(): void
    {
        config(['accounting.enforce_2fa' => false]);

        $user = User::factory()->create(['email_verified_at' => now()]);
        app(TeamManagementService::class)->createPersonalTeamForUser($user);
        $user = $user->fresh();
        $team = (int) $user->current_team_id;
        Role::findOrCreate('super_admin', 'web');
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $failures = [];
        $checked = 0;
        $skipped = [];

        foreach (Filament::getPanels() as $panelId => $panel) {
            foreach ($panel->getResources() as $resource) {
                $pages = array_keys($resource::getPages());
                $targets = array_values(array_intersect(['view', 'edit'], $pages));
                if ($targets === []) {
                    continue;
                }

                $record = $this->seedRecord($resource::getModel(), $team);
                if (! $record instanceof Model) {
                    $skipped[] = class_basename($resource);

                    continue;
                }

                foreach ($targets as $page) {
                    $name = "filament.{$panelId}.resources.{$resource::getSlug()}.{$page}";
                    if (! Route::has($name)) {
                        continue;
                    }

                    $params = ['record' => $record->getKey(), 'tenant' => $team];
                    $checked++;
                    try {
                        $response = $this->get(route($name, $params));
                        if ($response->status() >= 500) {
                            $msg = $response->exception?->getMessage() ?? 'HTTP '.$response->status();
                            $failures[] = "{$panelId}/".class_basename($resource)."/{$page} -> ".Str::limit($msg, 160);
                        }
                    } catch (\Throwable $e) {
                        $failures[] = "{$panelId}/".class_basename($resource)."/{$page} -> EXC ".Str::limit($e->getMessage(), 160);
                    }
                }
            }
        }

        fwrite(STDERR, "\n[edit-view-smoke] checked {$checked}, ".count($failures).' failed, '.count($skipped)." skipped (no factory/record)\n");
        foreach ($failures as $f) {
            fwrite(STDERR, "  ✗ {$f}\n");
        }
        if ($skipped !== []) {
            fwrite(STDERR, '  (skipped: '.implode(', ', $skipped).")\n");
        }

        $this->assertSame([], $failures, count($failures).' Filament edit/view page(s) failed to render');
    }

    /** Best-effort: create one record of $model scoped to $team, or null if it can't be seeded. */
    private function seedRecord(string $model, int $team): ?Model
    {
        try {
            $record = $model::factory()->create();
        } catch (\Throwable) {
            return null; // no factory, or required relations the factory can't satisfy
        }

        if (Schema::hasColumn($record->getTable(), 'team_id')) {
            $record->forceFill(['team_id' => $team])->save();
        }

        return $record;
    }
}
