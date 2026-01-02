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
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\EnsureUserHasCompany;
use Illuminate\Support\Facades\Route;

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
Route::prefix('cabinet')->name('cabinet.')->middleware(['auth', \App\Http\Middleware\SetCabinetLocale::class, EnsureUserHasCompany::class])->group(function () {
    // Company management (must be before dashboard for users without company)
    Route::get('/company/create', [\App\Http\Controllers\Cabinet\CompanyController::class, 'create'])->name('company.create');
    Route::post('/company', [\App\Http\Controllers\Cabinet\CompanyController::class, 'store'])->name('company.store');
    Route::get('/company', [\App\Http\Controllers\Cabinet\CompanyController::class, 'show'])->name('company.show');
    Route::get('/company/edit', [\App\Http\Controllers\Cabinet\CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company', [\App\Http\Controllers\Cabinet\CompanyController::class, 'update'])->name('company.update');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    
    // SKUs (Products)
    Route::get('/skus', [\App\Http\Controllers\Cabinet\SkuController::class, 'index'])->name('skus.index');
    Route::get('/skus/create', [\App\Http\Controllers\Cabinet\SkuController::class, 'create'])->name('skus.create');
    Route::post('/skus', [\App\Http\Controllers\Cabinet\SkuController::class, 'store'])->name('skus.store');
    Route::get('/skus/{sku}/edit', [\App\Http\Controllers\Cabinet\SkuController::class, 'edit'])->name('skus.edit');
    Route::put('/skus/{sku}', [\App\Http\Controllers\Cabinet\SkuController::class, 'update'])->name('skus.update');
    Route::delete('/skus/{sku}', [\App\Http\Controllers\Cabinet\SkuController::class, 'destroy'])->name('skus.destroy');
    
    // Inbounds (ASN)
    Route::get('/inbounds', [InboundController::class, 'index'])->name('inbounds.index');
    Route::get('/inbounds/create', [InboundController::class, 'create'])->name('inbounds.create');
    Route::post('/inbounds', [InboundController::class, 'store'])->name('inbounds.store');
    Route::get('/inbounds/{inbound}', [InboundController::class, 'show'])->name('inbounds.show');
    Route::get('/inbounds/{inbound}/edit', [InboundController::class, 'edit'])->name('inbounds.edit');
    Route::put('/inbounds/{inbound}', [InboundController::class, 'update'])->name('inbounds.update');
    
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
    Route::get('/finance/invoices/{invoice}', [FinanceController::class, 'show'])->name('finance.invoices.show');
    Route::get('/finance/payments', [FinanceController::class, 'payments'])->name('finance.payments');
    
    // Invoice payment routes
    Route::get('/finance/invoices/{invoice}/pay', [InvoicePaymentController::class, 'pay'])->name('finance.invoices.pay');
    Route::post('/finance/invoices/{invoice}/pay/click', [InvoicePaymentController::class, 'initiateClick'])->name('finance.invoices.pay.click');
    Route::post('/finance/invoices/{invoice}/pay/payme', [InvoicePaymentController::class, 'initiatePayme'])->name('finance.invoices.pay.payme');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/locale', [ProfileController::class, 'updateLocale'])->name('profile.locale');
    Route::post('/profile/switch-company/{company}', [ProfileController::class, 'switchCompany'])->name('profile.switch-company');
});

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
    Route::post('/calculator/clear-history', [CalculatorController::class, 'clearHistory'])->name('calculator.clear-history');
    
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

Route::post('/payment/click', [PaymentController::class, 'clickCallback'])->name('payment.click.callback');
Route::get('/payment/click/return', [PaymentController::class, 'clickReturn'])->name('payment.click.return');
Route::post('/payment/payme', [PaymentController::class, 'paymeCallback'])->name('payment.payme.callback');
