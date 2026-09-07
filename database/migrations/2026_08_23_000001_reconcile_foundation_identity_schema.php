<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)->default('en')->after('email');
            }
            if (! Schema::hasColumn('users', 'theme_preference')) {
                $table->string('theme_preference')->nullable()->default('default')->after('email');
            }
            if (! Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone', 64)->nullable();
            }
            if (! Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable();
            }
            if (! Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable();
            }
            if (! Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable();
            }
        });

        $columnNames = config('permission.column_names', []);
        $teamColumn = $columnNames['team_foreign_key'] ?? 'team_id';
        $modelColumn = $columnNames['model_morph_key'] ?? 'model_id';

        foreach ([
            'model_has_roles' => $columnNames['role_pivot_key'] ?? 'role_id',
            'model_has_permissions' => $columnNames['permission_pivot_key'] ?? 'permission_id',
        ] as $tableName => $pivotColumn) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, $teamColumn)) {
                continue;
            }

            $primary = collect(Schema::getIndexes($tableName))->firstWhere('primary', true);

            if ($primary !== null) {
                // PostgreSQL cannot alter a column that participates in a
                // primary-key constraint. Dropping the key through the schema
                // builder and changing the column in the same migration can
                // be reordered by the grammar, so remove the constraint with
                // a separate statement first. The schema-builder path remains
                // useful for MySQL and SQLite.
                if (DB::connection()->getDriverName() === 'pgsql') {
                    $table = DB::connection()->getQueryGrammar()->wrapTable($tableName);
                    $constraintName = DB::selectOne(
                        'select c.conname from pg_constraint c join pg_class t on t.oid = c.conrelid join pg_namespace n on n.oid = t.relnamespace where c.contype = ? and t.relname = ? and n.nspname = current_schema()',
                        ['p', $tableName],
                    )?->conname ?? $primary['name'];
                    $constraint = DB::connection()->getQueryGrammar()->wrap($constraintName);

                    DB::statement("alter table {$table} drop constraint if exists {$constraint}");
                } else {
                    Schema::table($tableName, function (Blueprint $table) use ($primary): void {
                        $table->dropPrimary($primary['name']);
                    });
                }
            }

            if (DB::connection()->getDriverName() === 'pgsql') {
                $table = DB::connection()->getQueryGrammar()->wrapTable($tableName);
                $column = DB::connection()->getQueryGrammar()->wrap($teamColumn);

                DB::statement("alter table {$table} alter column {$column} type bigint using {$column}::bigint");
                DB::statement("alter table {$table} alter column {$column} drop not null");
            } else {
                Schema::table($tableName, function (Blueprint $table) use ($teamColumn): void {
                    $table->unsignedBigInteger($teamColumn)->nullable()->change();
                });
            }

            $uniqueName = "{$tableName}_team_pivot_unique";
            $hasUnique = collect(Schema::getIndexes($tableName))->contains(
                fn (array $index): bool => $index['name'] === $uniqueName,
            );

            if (! $hasUnique) {
                Schema::table($tableName, function (Blueprint $table) use ($teamColumn, $pivotColumn, $modelColumn, $uniqueName): void {
                    $table->unique([$teamColumn, $pivotColumn, $modelColumn, 'model_type'], $uniqueName);
                });
            }
        }
    }

    public function down(): void
    {
        // Identity columns are intentionally retained for safe rollback across
        // installations where the foundation migrations own their lifecycle.
    }
};
