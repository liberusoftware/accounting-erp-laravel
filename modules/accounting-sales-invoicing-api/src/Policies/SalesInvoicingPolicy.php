<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingApi\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;

final class SalesInvoicingPolicy
{
    private function can(?Authenticatable $u, string $a): bool
    {
        return $u !== null && method_exists($u, 'tokenCan') && $u->tokenCan($a);
    }

    public function viewAny(?Authenticatable $u): bool
    {
        return $this->can($u, 'accounting.invoices.read');
    }

    public function view(?Authenticatable $u, SalesInvoice $i): bool
    {
        return $this->can($u, 'accounting.invoices.read');
    }

    public function create(?Authenticatable $u): bool
    {
        return $this->can($u, 'accounting.invoices.write');
    }

    public function update(?Authenticatable $u, SalesInvoice $i): bool
    {
        return $this->can($u, 'accounting.invoices.write');
    }

    public function delete(?Authenticatable $u, SalesInvoice $i): bool
    {
        return $this->can($u, 'accounting.invoices.write');
    }
}
