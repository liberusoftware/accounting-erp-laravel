<?php

use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\AccountTemplate;
use Liberu\Accounting\Erp\Models\ApprovalRequest;
use Liberu\Accounting\Erp\Models\ApprovalRule;
use Liberu\Accounting\Erp\Models\ApprovalStep;
use Liberu\Accounting\Erp\Models\Asset;
use Liberu\Accounting\Erp\Models\AssetAcquisition;
use Liberu\Accounting\Erp\Models\AuditLog;
use Liberu\Accounting\Erp\Models\BankAccountBalance;
use Liberu\Accounting\Erp\Models\BankConnection;
use Liberu\Accounting\Erp\Models\BankFeedTransaction;
use Liberu\Accounting\Erp\Models\BankStatement;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\Erp\Models\BillItem;
use Liberu\Accounting\Erp\Models\BillPayment;
use Liberu\Accounting\Erp\Models\Budget;
use Liberu\Accounting\Erp\Models\Category;
use Liberu\Accounting\Erp\Models\Company;
use Liberu\Accounting\Erp\Models\ConnectedAccount;
use Liberu\Accounting\Erp\Models\ConsolidationGroup;
use Liberu\Accounting\Erp\Models\CreditMemo;
use Liberu\Accounting\Erp\Models\CreditMemoApplication;
use Liberu\Accounting\Erp\Models\CreditMemoItem;
use Liberu\Accounting\Erp\Models\Currency;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\DelayedCharge;
use Liberu\Accounting\Erp\Models\DepreciationCalculation;
use Liberu\Accounting\Erp\Models\Document;
use Liberu\Accounting\Erp\Models\DocumentVersion;
use Liberu\Accounting\Erp\Models\domains;
use Liberu\Accounting\Erp\Models\Employee;
use Liberu\Accounting\Erp\Models\Estimate;
use Liberu\Accounting\Erp\Models\EstimateItem;
use Liberu\Accounting\Erp\Models\ExchangeRate;
use Liberu\Accounting\Erp\Models\Expense;
use Liberu\Accounting\Erp\Models\ForecastScenario;
use Liberu\Accounting\Erp\Models\ForecastScenarioLine;
use Liberu\Accounting\Erp\Models\HmrcCorporationTaxSubmission;
use Liberu\Accounting\Erp\Models\HmrcPayeSubmission;
use Liberu\Accounting\Erp\Models\HmrcSubmission;
use Liberu\Accounting\Erp\Models\HmrcVatReturn;
use Liberu\Accounting\Erp\Models\InventoryCostLayer;
use Liberu\Accounting\Erp\Models\InventoryItem;
use Liberu\Accounting\Erp\Models\InventoryTransaction;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\InvoiceItem;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\JournalEntryLine;
use Liberu\Accounting\Erp\Models\Membership;
use Liberu\Accounting\Erp\Models\Menu;
use Liberu\Accounting\Erp\Models\Module;
use Liberu\Accounting\Erp\Models\OutboundPayment;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\PaymentTerm;
use Liberu\Accounting\Erp\Models\Payroll;
use Liberu\Accounting\Erp\Models\Plan;
use Liberu\Accounting\Erp\Models\PremiumCatalog;
use Liberu\Accounting\Erp\Models\Project;
use Liberu\Accounting\Erp\Models\PurchaseOrder;
use Liberu\Accounting\Erp\Models\PurchaseOrderItem;
use Liberu\Accounting\Erp\Models\PurchaseRequest;
use Liberu\Accounting\Erp\Models\PurchaseRequestItem;
use Liberu\Accounting\Erp\Models\QboConnection;
use Liberu\Accounting\Erp\Models\ReconciliationRule;
use Liberu\Accounting\Erp\Models\RefundReceipt;
use Liberu\Accounting\Erp\Models\RefundReceiptItem;
use Liberu\Accounting\Erp\Models\ReminderSetting;
use Liberu\Accounting\Erp\Models\RevenueSchedule;
use Liberu\Accounting\Erp\Models\RevenueScheduleEntry;
use Liberu\Accounting\Erp\Models\Role;
use Liberu\Accounting\Erp\Models\SageConnection;
use Liberu\Accounting\Erp\Models\SalesOrder;
use Liberu\Accounting\Erp\Models\SalesOrderItem;
use Liberu\Accounting\Erp\Models\SalesReceipt;
use Liberu\Accounting\Erp\Models\SalesReceiptItem;
use Liberu\Accounting\Erp\Models\Subscription;
use Liberu\Accounting\Erp\Models\Supplier;
use Liberu\Accounting\Erp\Models\TaxForm;
use Liberu\Accounting\Erp\Models\TaxRate;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\TeamInvitation;
use Liberu\Accounting\Erp\Models\TimeEntry;
use Liberu\Accounting\Erp\Models\Transaction;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Models\UserNotificationPreference;
use Liberu\Accounting\Erp\Models\Vendor;
use Liberu\Accounting\Erp\Models\VendorCredit;
use Liberu\Accounting\Erp\Models\VendorCreditApplication;
use Liberu\Accounting\Erp\Models\VendorCreditItem;
use Liberu\Accounting\Erp\Models\XeroConnection;

return [
    'App\\Models\\Account' => Account::class,
    'App\\Models\\AccountTemplate' => AccountTemplate::class,
    'App\\Models\\ApprovalRequest' => ApprovalRequest::class,
    'App\\Models\\ApprovalRule' => ApprovalRule::class,
    'App\\Models\\ApprovalStep' => ApprovalStep::class,
    'App\\Models\\Asset' => Asset::class,
    'App\\Models\\AssetAcquisition' => AssetAcquisition::class,
    'App\\Models\\AuditLog' => AuditLog::class,
    'App\\Models\\BankAccountBalance' => BankAccountBalance::class,
    'App\\Models\\BankConnection' => BankConnection::class,
    'App\\Models\\BankFeedTransaction' => BankFeedTransaction::class,
    'App\\Models\\BankStatement' => BankStatement::class,
    'App\\Models\\Bill' => Bill::class,
    'App\\Models\\BillItem' => BillItem::class,
    'App\\Models\\BillPayment' => BillPayment::class,
    'App\\Models\\Budget' => Budget::class,
    'App\\Models\\Category' => Category::class,
    'App\\Models\\Company' => Company::class,
    'App\\Models\\ConnectedAccount' => ConnectedAccount::class,
    'App\\Models\\ConsolidationGroup' => ConsolidationGroup::class,
    'App\\Models\\CreditMemoApplication' => CreditMemoApplication::class,
    'App\\Models\\CreditMemoItem' => CreditMemoItem::class,
    'App\\Models\\Currency' => Currency::class,
    'App\\Models\\Customer' => Customer::class,
    'App\\Models\\DelayedCharge' => DelayedCharge::class,
    'App\\Models\\DepreciationCalculation' => DepreciationCalculation::class,
    'App\\Models\\Document' => Document::class,
    'App\\Models\\DocumentVersion' => DocumentVersion::class,
    'App\\Models\\Employee' => Employee::class,
    'App\\Models\\Estimate' => Estimate::class,
    'App\\Models\\EstimateItem' => EstimateItem::class,
    'App\\Models\\ExchangeRate' => ExchangeRate::class,
    'App\\Models\\Expense' => Expense::class,
    'App\\Models\\ForecastScenario' => ForecastScenario::class,
    'App\\Models\\ForecastScenarioLine' => ForecastScenarioLine::class,
    'App\\Models\\HmrcCorporationTaxSubmission' => HmrcCorporationTaxSubmission::class,
    'App\\Models\\HmrcPayeSubmission' => HmrcPayeSubmission::class,
    'App\\Models\\HmrcSubmission' => HmrcSubmission::class,
    'App\\Models\\HmrcVatReturn' => HmrcVatReturn::class,
    'App\\Models\\InventoryCostLayer' => InventoryCostLayer::class,
    'App\\Models\\InventoryItem' => InventoryItem::class,
    'App\\Models\\InventoryTransaction' => InventoryTransaction::class,
    'App\\Models\\Invoice' => Invoice::class,
    'App\\Models\\InvoiceItem' => InvoiceItem::class,
    'App\\Models\\JournalEntry' => JournalEntry::class,
    'App\\Models\\JournalEntryLine' => JournalEntryLine::class,
    'App\\Models\\Membership' => Membership::class,
    'App\\Models\\Menu' => Menu::class,
    'App\\Models\\Module' => Module::class,
    'App\\Models\\OutboundPayment' => OutboundPayment::class,
    'App\\Models\\Payment' => Payment::class,
    'App\\Models\\PaymentTerm' => PaymentTerm::class,
    'App\\Models\\Payroll' => Payroll::class,
    'App\\Models\\Plan' => Plan::class,
    'App\\Models\\PremiumCatalog' => PremiumCatalog::class,
    'App\\Models\\Project' => Project::class,
    'App\\Models\\PurchaseOrder' => PurchaseOrder::class,
    'App\\Models\\PurchaseOrderItem' => PurchaseOrderItem::class,
    'App\\Models\\PurchaseRequest' => PurchaseRequest::class,
    'App\\Models\\PurchaseRequestItem' => PurchaseRequestItem::class,
    'App\\Models\\QboConnection' => QboConnection::class,
    'App\\Models\\ReconciliationRule' => ReconciliationRule::class,
    'App\\Models\\RefundReceipt' => RefundReceipt::class,
    'App\\Models\\RefundReceiptItem' => RefundReceiptItem::class,
    'App\\Models\\ReminderSetting' => ReminderSetting::class,
    'App\\Models\\RevenueSchedule' => RevenueSchedule::class,
    'App\\Models\\RevenueScheduleEntry' => RevenueScheduleEntry::class,
    'App\\Models\\Role' => Role::class,
    'App\\Models\\SageConnection' => SageConnection::class,
    'App\\Models\\SalesOrder' => SalesOrder::class,
    'App\\Models\\SalesOrderItem' => SalesOrderItem::class,
    'App\\Models\\SalesReceipt' => SalesReceipt::class,
    'App\\Models\\SalesReceiptItem' => SalesReceiptItem::class,
    'App\\Models\\Subscription' => Subscription::class,
    'App\\Models\\Supplier' => Supplier::class,
    'App\\Models\\TaxForm' => TaxForm::class,
    'App\\Models\\TaxRate' => TaxRate::class,
    'App\\Models\\Team' => Team::class,
    'App\\Models\\TeamInvitation' => TeamInvitation::class,
    'App\\Models\\TimeEntry' => TimeEntry::class,
    'App\\Models\\Transaction' => Transaction::class,
    'App\\Models\\UserNotificationPreference' => UserNotificationPreference::class,
    'App\\Models\\Vendor' => Vendor::class,
    'App\\Models\\VendorCredit' => VendorCredit::class,
    'App\\Models\\VendorCreditApplication' => VendorCreditApplication::class,
    'App\\Models\\VendorCreditItem' => VendorCreditItem::class,
    'App\\Models\\XeroConnection' => XeroConnection::class,
    'App\\Models\\domains' => domains::class,
    'App\\Models\\CreditMemo' => CreditMemo::class,
    'App\\Models\\User' => User::class,
];
