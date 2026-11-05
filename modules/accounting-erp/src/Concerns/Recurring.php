<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Shared recurrence engine for Invoice / Bill / Expense.
 *
 * A model with `is_recurring = true` is a TEMPLATE (config, not a billed
 * occurrence). `generateDue()` walks from `recurrence_start` forward one
 * frequency-step at a time, generating a draft child for every occurrence
 * date <= today that hasn't been generated yet (catch-up), bounded by
 * SAFETY_CAP per run. `last_generated` is advanced + persisted per
 * occurrence so a crash never re-generates an already-created child.
 *
 * Each using model MUST implement recurringDateColumns(); the other hooks
 * have safe defaults (no number column, no line items, no status resets).
 *
 * @mixin Model
 */
trait Recurring
{
    /** Max occurrences generated for one template in a single run. */
    protected int $recurringSafetyCap = 120;

    /**
     * Generate every occurrence due since last_generated. Returns the count
     * created this run. Idempotent: an immediate re-run generates 0.
     */
    public function generateDue(): int
    {
        if (! $this->is_recurring || $this->recurrence_start === null) {
            return 0;
        }

        if (! in_array($this->recurrence_frequency, ['daily', 'weekly', 'monthly', 'yearly'], true)) {
            return 0;
        }

        $cursor = $this->last_generated
            ? $this->nextRecurringDate($this->last_generated)
            : $this->recurrence_start->copy();

        $today = today();
        $end = $this->recurrence_end;
        $count = 0;

        while ($cursor->lte($today) && ($end === null || $cursor->lte($end))) {
            if ($count >= $this->recurringSafetyCap) {
                Log::warning('Recurring safety cap hit; remaining occurrences resume next run.', [
                    'model' => static::class,
                    'id' => $this->getKey(),
                    'cap' => $this->recurringSafetyCap,
                ]);
                break;
            }

            // One occurrence + its last_generated advance are atomic: if item
            // cloning throws, the child rolls back and last_generated stays put,
            // so the next run regenerates cleanly — no orphan, no duplicate.
            DB::transaction(function () use ($cursor): void {
                $this->makeRecurringOccurrence($cursor);
                $this->last_generated = $cursor->copy();
                $this->save();
            });

            $cursor = $this->nextRecurringDate($cursor);
            $count++;
        }

        return $count;
    }

    /**
     * Next occurrence date after $from. copy() avoids mutating the source
     * Carbon (date casts are mutable — this was the original bug).
     */
    protected function nextRecurringDate(Carbon $from): Carbon
    {
        return match ($this->recurrence_frequency) {
            'daily' => $from->copy()->addDay(),
            'weekly' => $from->copy()->addWeek(),
            'monthly' => $from->copy()->addMonth(),
            'yearly' => $from->copy()->addYear(),
            default => $from->copy(),
        };
    }

    /**
     * Clone the template into one persisted draft occurrence dated $date.
     * team_id rides along via replicate(), so tenancy is preserved.
     */
    protected function makeRecurringOccurrence(Carbon $date): static
    {
        $child = $this->replicate();

        // A generated occurrence is not itself a template.
        $child->is_recurring = false;
        $child->recurrence_frequency = null;
        $child->recurrence_start = null;
        $child->recurrence_end = null;
        $child->last_generated = null;

        // Null the number column so the model's creating hook regenerates a
        // fresh unique one (copying it would violate uniqueness).
        $numberColumn = $this->recurringNumberColumn();
        if ($numberColumn !== null) {
            $child->{$numberColumn} = null;
        }

        foreach ($this->recurringDraftAttributes() as $key => $value) {
            $child->{$key} = $value;
        }

        foreach ($this->recurringDateColumns($date) as $key => $value) {
            $child->{$key} = $value;
        }

        $child->save();

        // Clone line items (replicate copies attributes, not relations).
        $itemsRelation = $this->recurringItemsRelation();
        if ($itemsRelation !== null) {
            /** @var HasMany<Model, static> $relation */
            $relation = $this->{$itemsRelation}();
            $foreignKey = $relation->getForeignKeyName();

            /** @var iterable<Model> $items */
            $items = $this->{$itemsRelation};
            foreach ($items as $item) {
                $copy = $item->replicate();
                $copy->{$foreignKey} = $child->getKey();
                $copy->save();
            }
        }

        return $child;
    }

    /**
     * Column holding the document number to null out (regenerated by the
     * model's creating hook). Null = model has no number column (Expense).
     */
    protected function recurringNumberColumn(): ?string
    {
        return null;
    }

    /**
     * hasMany relation name for line items to clone. Null = single-row model
     * with no line items (Expense).
     */
    protected function recurringItemsRelation(): ?string
    {
        return null;
    }

    /**
     * Status/ownership resets applied to each generated draft.
     *
     * @return array<string, mixed>
     */
    protected function recurringDraftAttributes(): array
    {
        return [];
    }

    /**
     * Map the occurrence date onto this model's date column(s), e.g.
     * ['invoice_date' => $date, 'due_date' => $date->copy()->addDays(30)].
     *
     * @return array<string, mixed>
     */
    abstract protected function recurringDateColumns(Carbon $date): array;
}
