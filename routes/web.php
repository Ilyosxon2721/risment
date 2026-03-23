<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MarketplaceServicesController;
use App\Http\Controllers\Cabinet\DashboardController;
use App\Http\Controllers\Cabinet\InventoryController;
use App\Http\Controllers\Cabinet\InboundController;
use App\Http\Controllers\Cabinet\ShipmentController;
use App\Http\Controllers\Cabinet\TicketController;
use App\Http\Controllers\Cabinet\ProfileController;
use App\Http\Controllers\Cabinet\SubscriptionController;
use App\Http\Controllers\Cabinet\FinanceController;
use App\Http\Controllers\Cabinet\InvoicePaymentController;
use App\Http\Controllers\Cabinet\IntegrationsController;
use App\Http\Controllers\Cabinet\SellermindLinkController;
use App\Http\Controllers\Cabinet\MarketplaceCredentialController;
use App\Http\Controllers\Cabinet\BillingReportController;
use App\Http\Controllers\Cabinet\CalculatorResultController;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\EnsureUserHasCompany;
use Illuminate\Support\Facades\Route;

// PWA offline fallback page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Redirect root to Russian
Route::get('/', function () {
    return redirect('/ru');
});

// Redirect non-localized auth routes to Russian
Route::get('/login', function () {
    return redirect('/ru/login');
});

Route::get('/register', function () {
    return redirect('/ru/register');
});

// Client Cabinet (NO locale prefix, uses user's saved locale preference)
// IMPORTANT: Must be defined BEFORE {locale} routes to prevent wildcard capture
Route::prefix('cabinet')->name('cabinet.')->middleware(['auth', \App\Http\Middleware\PanelSessionIsolation::class.':cabinet', \App\Http\Middleware\SetCabinetLocale::class, EnsureUserHasCompany::class])->group(function () {
    // Company management (must be before dashboard for users without company)
    Route::get('/company/create', [\App\Http\Controllers\Cabinet\CompanyController::class, 'create'])->name('company.create');
    Route::post('/company', [\App\Http\Controllers\Cabinet\CompanyController::class, 'store'])->name('company.store');
    Route::get('/company', [\App\Http\Controllers\Cabinet\CompanyController::class, 'show'])->name('company.show');
    Route::get('/company/edit', [\App\Http\Controllers\Cabinet\CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company', [\App\Http\Controllers\Cabinet\CompanyController::class, 'update'])->name('company.update');

    // Dashboard
    Route::get('/dashboard', fn () => redirect()->route('cabinet.dashboard'));
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/calculator/estimate', [DashboardController::class, 'estimate'])->name('calculator.estimate');
    
    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    
    // SKUs (Products)
    Route::get('/skus', [\App\Http\Controllers\Cabinet\SkuController::class, 'index'])->name('skus.index');
    Route::get('/skus/create', [\App\Http\Controllers\Cabinet\SkuController::class, 'create'])->name('skus.create');
    Route::post('/skus', [\App\Http\Controllers\Cabinet\SkuController::class, 'store'])->name('skus.store');
    Route::get('/skus/{sku}/edit', [\App\Http\Controllers\Cabinet\SkuController::class, 'edit'])->name('skus.edit');
    Route::put('/skus/{sku}', [\App\Http\Controllers\Cabinet\SkuController::class, 'update'])->name('skus.update');
    Route::delete('/skus/{sku}', [\App\Http\Controllers\Cabinet\SkuController::class, 'destroy'])->name('skus.destroy');
    
    // Products (New system with variants)
    Route::resource('products', \App\Http\Controllers\Cabinet\ProductController::class);
    Route::post('/products/{product}/toggle-status', [\App\Http\Controllers\Cabinet\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('/products/{product}/resync', [\App\Http\Controllers\Cabinet\ProductController::class, 'resync'])->name('products.resync');
    
    // Inbounds
    Route::resource('inbounds', InboundController::class);
    Route::post('/inbounds/{inbound}/submit', [InboundController::class, 'submit'])->name('inbounds.submit');
    Route::post('/inbounds/{inbound}/confirm', [InboundController::class, 'confirm'])->name('inbounds.confirm');
    
    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    
    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    
    // Subscription
    Route::get('/subscription/choose', [SubscriptionController::class, 'choose'])->name('subscription.choose');
    Route::post('/subscription/select/{plan}', [SubscriptionController::class, 'select'])->name('subscription.select');
    Route::get('/subscription/confirm', [SubscriptionController::class, 'confirm'])->name('subscription.confirm');
    
    // Finance
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/invoices', [FinanceController::class, 'invoices'])->name('finance.invoices.index');
    Route::get('/finance/payments', [FinanceController::class, 'payments'])->name('finance.payments');

    // Payment result pages (must be before {invoice} wildcard)
    Route::get('/finance/invoices/payment-success', [InvoicePaymentController::class, 'paymentSuccess'])->name('finance.invoices.payment-success');
    Route::get('/finance/invoices/payment-failed', [InvoicePaymentController::class, 'paymentFailed'])->name('finance.invoices.payment-failed');

    Route::get('/finance/invoices/{invoice}', [FinanceController::class, 'show'])->name('finance.invoices.show');

    // Invoice payment routes
    Route::get('/finance/invoices/{invoice}/pay', [InvoicePaymentController::class, 'pay'])->name('finance.invoices.pay');
    Route::post('/finance/invoices/{invoice}/pay/click', [InvoicePaymentController::class, 'initiateClick'])->middleware('throttle:10,1')->name('finance.invoices.pay.click');
    Route::post('/finance/invoices/{invoice}/pay/payme', [InvoicePaymentController::class, 'initiatePayme'])->middleware('throttle:10,1')->name('finance.invoices.pay.payme');

    // Integrations hub
    Route::get('/integrations', [IntegrationsController::class, 'index'])->name('integrations.index');

    // SellerMind Integration (under /integrations/sellermind)
    Route::get('/integrations/sellermind', [SellermindLinkController::class, 'index'])->name('integrations.sellermind');
    Route::post('/integrations/sellermind/generate', [SellermindLinkController::class, 'generateToken'])->middleware('throttle:5,1')->name('integrations.sellermind.generate');
    Route::post('/integrations/sellermind/regenerate', [SellermindLinkController::class, 'regenerateToken'])->middleware('throttle:5,1')->name('integrations.sellermind.regenerate');
    Route::put('/integrations/sellermind/settings', [SellermindLinkController::class, 'updateSettings'])->name('integrations.sellermind.settings');
    Route::post('/integrations/sellermind/sync-all', [SellermindLinkController::class, 'syncAll'])->middleware('throttle:5,1')->name('integrations.sellermind.sync-all');
    Route::post('/integrations/sellermind/check-status', [SellermindLinkController::class, 'checkStatus'])->middleware('throttle:10,1')->name('integrations.sellermind.check-status');
    Route::delete('/integrations/sellermind', [SellermindLinkController::class, 'disconnect'])->name('integrations.sellermind.disconnect');

    // Marketplace Credentials
    Route::resource('marketplaces', MarketplaceCredentialController::class)->except(['show']);

    // Old sellermind routes → redirect to new location
    Route::get('/sellermind', fn () => redirect()->route('cabinet.integrations.sellermind'))->name('sellermind.index');

    // Billing Report
    Route::get('/billing', [BillingReportController::class, 'index'])->name('billing.report');
    Route::get('/billing/invoices/{billingInvoice}', [BillingReportController::class, 'showInvoice'])->name('billing.invoice');
    Route::get('/billing/transactions', [BillingReportController::class, 'transactions'])->name('billing.transactions');
    Route::get('/billing/charges', [BillingReportController::class, 'charges'])->name('billing.charges');

    // Saved Calculations
    Route::get('/calculations', [CalculatorResultController::class, 'index'])->name('calculations.index');
    Route::delete('/calculations/{calculation}', [CalculatorResultController::class, 'destroy'])->name('calculations.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:5,1')->name('profile.password');
    Route::put('/profile/locale', [ProfileController::class, 'updateLocale'])->name('profile.locale');
    Route::post('/profile/switch-company/{company}', [ProfileController::class, 'switchCompany'])->name('profile.switch-company');
});

// Manager Authentication (separate guard)
use App\Http\Controllers\Manager\AuthController as ManagerAuthController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\TaskController as ManagerTaskController;
use App\Http\Controllers\Manager\ConfirmationController as ManagerConfirmationController;
use App\Http\Controllers\Manager\BillingController as ManagerBillingController;

Route::prefix('manager')->name('manager.')->group(function () {
    Route::get('login', [ManagerAuthController::class, 'showLoginForm'])
        ->middleware('guest:manager')->name('login');
    Route::post('login', [ManagerAuthController::class, 'login'])
        ->middleware(['guest:manager', 'throttle:5,1'])->name('login.submit');
    Route::post('logout', [ManagerAuthController::class, 'logout'])
        ->middleware('auth:manager')->name('logout');
});

// Manager Cabinet (uses separate 'manager' guard)
Route::prefix('manager')->name('manager.')->middleware([
    'auth:manager',
    \App\Http\Middleware\PanelSessionIsolation::class.':manager',
    \App\Http\Middleware\SetCabinetLocale::class,
    \App\Http\Middleware\EnsureUserIsManager::class,
    \App\Http\Middleware\SetManagerCompany::class,
])->group(function () {
    Route::get('/', [ManagerDashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-company/{company}', [ManagerDashboardController::class, 'switchCompany'])->name('switch-company');

    // Tasks
    Route::get('/tasks', [ManagerTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [ManagerTaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [ManagerTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [ManagerTaskController::class, 'show'])->name('tasks.show');

    // SellerMind confirmations
    Route::get('/confirmations', [ManagerConfirmationController::class, 'index'])->name('confirmations.index');
    Route::post('/confirmations/{task}/confirm', [ManagerConfirmationController::class, 'confirm'])->name('confirmations.confirm');
    Route::post('/confirmations/{task}/reject', [ManagerConfirmationController::class, 'reject'])->name('confirmations.reject');

    // Billing overview
    Route::get('/billing', [ManagerBillingController::class, 'index'])->name('billing.index');

    // Inventory
    Route::get('/inventory', [\App\Http\Controllers\Manager\InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/{inventory}', [\App\Http\Controllers\Manager\InventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/{inventory}/adjust', [\App\Http\Controllers\Manager\InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Shipments
    Route::get('/shipments', [\App\Http\Controllers\Manager\ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{shipment}', [\App\Http\Controllers\Manager\ShipmentController::class, 'show'])->name('shipments.show');
    Route::post('/shipments/{shipment}/status', [\App\Http\Controllers\Manager\ShipmentController::class, 'updateStatus'])->name('shipments.status');

    // Inbounds
    Route::get('/inbounds', [\App\Http\Controllers\Manager\InboundController::class, 'index'])->name('inbounds.index');
    Route::get('/inbounds/{inbound}', [\App\Http\Controllers\Manager\InboundController::class, 'show'])->name('inbounds.show');
    Route::post('/inbounds/{inbound}/receive', [\App\Http\Controllers\Manager\InboundController::class, 'receive'])->name('inbounds.receive');
});

// No-companies page (without SetManagerCompany middleware)
Route::get('/manager/no-companies', function () {
    return view('manager.no-companies');
})->middleware(['auth:manager', \App\Http\Middleware\SetCabinetLocale::class, \App\Http\Middleware\EnsureUserIsManager::class])->name('manager.no-companies');

// Localized
// Routes with locale prefix (public pages)
Route::prefix('{locale}')->middleware(SetLocale::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Auth routes with locale
    require __DIR__.'/auth.php';
    
    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    
    // Marketplace services (MUST be before /services/{slug} wildcard)
    Route::get('/services/marketplace', [MarketplaceServicesController::class, 'index'])->name('services.marketplace');
    Route::get('/calculators/marketplace', [MarketplaceServicesController::class, 'calculator'])->name('calculators.marketplace');
    Route::post('/calculators/marketplace', [MarketplaceServicesController::class, 'calculate'])->name('calculators.marketplace.calculate');
    
    Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
    
    // Pricing & Calculator
    Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
    Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator');
    Route::post('/calculator', [CalculatorController::class, 'calculate'])->name('calculator.calculate');
    Route::post('/calculator/send-email', [CalculatorController::class, 'sendResults'])->name('calculator.send-email')->middleware('throttle:5,1');
    Route::post('/calculator/clear-history', [CalculatorController::class, 'clearHistory'])->name('calculator.clear-history');
    Route::post('/calculator/save', [CalculatorController::class, 'saveResult'])->name('calculator.save')->middleware('auth');
    
    // Content Pages
    Route::get('/sla', [PageController::class, 'sla'])->name('sla');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
    Route::get('/docs/{slug}', [DocumentController::class, 'show'])->name('docs.show');
    
    // Lead forms
    Route::post('/lead', [LeadController::class, 'store'])->name('lead.store')->middleware('throttle:5,1');
});

// Payment Gateway Callbacks (without CSRF protection)
use App\Http\Controllers\Payment\PaymentController;

Route::post('/payment/click', [PaymentController::class, 'clickCallback'])->middleware('throttle:30,1')->name('payment.click.callback');
Route::get('/payment/click/return', [PaymentController::class, 'clickReturn'])->name('payment.click.return');
Route::post('/payment/payme', [PaymentController::class, 'paymeCallback'])->middleware('throttle:30,1')->name('payment.payme.callback');
