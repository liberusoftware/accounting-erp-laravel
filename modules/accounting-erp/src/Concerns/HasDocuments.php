<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Liberu\Accounting\Erp\Models\Document;

trait HasDocuments
{
    /**
     * @return MorphMany<Document, $this>
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
