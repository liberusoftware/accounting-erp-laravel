# Accounting Supplier Bills

The supplier-bills capability owns supplier bill capture and lifecycle: draft, approval, posting, rejection, voiding, coded/taxed lines, due dates, duplicate detection, purchase-order/receipt matching references, credits, documents, recurrence metadata, and external identifiers. Posting creates an Accounts Payable open item through its public action boundary.

Presentation adapters are optional: `module-accounting-supplier-bills-api`, `module-accounting-supplier-bills-filament`, and `module-accounting-supplier-bills-livewire`. The domain package contains no Filament, Livewire, application `App\\`, or provider SDK dependency.
