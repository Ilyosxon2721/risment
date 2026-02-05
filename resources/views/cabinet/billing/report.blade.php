@extends('cabinet.layout')

@section('title', __('Billing Report'))

@section('content')
<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('Billing Report') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('Overview of charges, invoices, and account balance') }}</p>
</div>

<!-- Top Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Balance -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Balance') }}</div>
        <div class="text-h2 font-heading {{ $billingBalance->balance < 0 ? 'text-error' : 'text-success' }}">
            {{ number_format(abs($billingBalance->balance), 0, '', ' ') }} {{ __('UZS') }}
        </div>
        <div class="text-body-s {{ $billingBalance->balance < 0 ? 'text-error' : 'text-success' }}">
            {{ $billingBalance->balance < 0 ? __('Debt') : __('Credit') }}
        </div>
    </div>

    <!-- Current Plan -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Current Plan') }}</div>
        <div class="text-h3 font-heading text-brand">
            {{ $subscription ? $subscription->billingPlan->getName() : __('No plan') }}
        </div>
        @if($subscription)
            <div class="text-body-s text-text-muted">
                {{ number_format($subscription->billingPlan->monthly_fee, 0, '', ' ') }} {{ __('UZS/month') }}
            </div>
        @endif
    </div>

    <!-- Current Month Charges -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('This Month') }}</div>
        <div class="text-h2 font-heading text-warning">
            {{ $estimate ? number_format($estimate['total'], 0, '', ' ') : 0 }} {{ __('UZS') }}
        </div>
        <div class="text-body-s text-text-muted">{{ __('Current charges') }}</div>
    </div>

    <!-- Projected Month -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Month Forecast') }}</div>
        <div class="text-h2 font-heading text-info">
            {{ $estimate ? number_format($estimate['projected_total'] ?? 0, 0, '', ' ') : 0 }} {{ __('UZS') }}
        </div>
        <div class="text-body-s text-text-muted">{{ __('Projected total') }}</div>
    </div>
</div>

<!-- Current Charges Breakdown -->
@if($estimate)
<div class="card mb-8">
    <h2 class="text-h3 font-heading mb-6">{{ __('Current Period Breakdown') }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('Storage') }}</div>
            <div class="text-h4 font-heading">{{ number_format($estimate['storage']['total'] ?? 0, 0, '', ' ') }}</div>
            <div class="text-body-s text-text-muted">
                {{ $estimate['storage']['total_unit_days'] ?? 0 }} {{ __('unit-days') }}
            </div>
        </div>
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('Shipments') }}</div>
            <div class="text-h4 font-heading">{{ number_format($estimate['shipments']['total'] ?? 0, 0, '', ' ') }}</div>
            <div class="text-body-s text-text-muted">
                {{ $estimate['shipments']['total_count'] ?? 0 }} {{ __('orders') }}
            </div>
        </div>
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('Receiving') }}</div>
            <div class="text-h4 font-heading">{{ number_format($estimate['receiving']['total'] ?? 0, 0, '', ' ') }}</div>
            <div class="text-body-s text-text-muted">
                {{ $estimate['receiving']['total_units'] ?? 0 }} {{ __('units') }}
            </div>
        </div>
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('Returns') }}</div>
            <div class="text-h4 font-heading">{{ number_format($estimate['returns']['total'] ?? 0, 0, '', ' ') }}</div>
            <div class="text-body-s text-text-muted">
                @if($estimate['returns']['included_in_plan'] ?? false)
                    {{ __('Included in plan') }}
                @else
                    {{ $estimate['returns']['total_units'] ?? 0 }} {{ __('units') }}
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Charts -->
<div class="card mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-h3 font-heading">{{ __('Cost Trends') }}</h2>
        <span class="text-body-s text-text-muted">{{ __('Last 6 months') }}</span>
    </div>
    <div class="h-64">
        <canvas id="billingChart"></canvas>
    </div>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('cabinet.billing.charges') }}" class="card hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-brand/10 flex items-center justify-center">
            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
        </div>
        <div>
            <div class="font-semibold">{{ __('Начисления за услуги') }}</div>
            <div class="text-body-s text-text-muted">{{ __('Детализация доп. услуг') }}</div>
        </div>
    </a>
    <a href="{{ route('cabinet.billing.transactions') }}" class="card hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
        </div>
        <div>
            <div class="font-semibold">{{ __('История транзакций') }}</div>
            <div class="text-body-s text-text-muted">{{ __('Пополнения и списания') }}</div>
        </div>
    </a>
    <a href="{{ route('cabinet.subscription.choose') }}" class="card hover:shadow-lg transition flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-warning/10 flex items-center justify-center">
            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
        </div>
        <div>
            <div class="font-semibold">{{ __('Сменить тариф') }}</div>
            <div class="text-body-s text-text-muted">{{ __('Выбор тарифного плана') }}</div>
        </div>
    </a>
</div>

<!-- Two-column: Invoices and Transactions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Recent Invoices -->
    <div class="card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-h3 font-heading">{{ __('Recent Invoices') }}</h2>
        </div>
        @if($recentInvoices->count())
            <div class="space-y-3">
                @foreach($recentInvoices as $invoice)
                <a href="{{ route('cabinet.billing.invoice', $invoice) }}" class="flex justify-between items-center p-3 bg-bg-soft rounded-btn hover:shadow transition">
                    <div>
                        <div class="font-semibold">{{ $invoice->invoice_number }}</div>
                        <div class="text-body-s text-text-muted">
                            {{ $invoice->period_start->format('d.m') }} - {{ $invoice->period_end->format('d.m.Y') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold">{{ number_format($invoice->total, 0, '', ' ') }} {{ __('UZS') }}</div>
                        <span class="badge {{ $invoice->getStatusBadgeClass() }}">{{ $invoice->getStatusLabel() }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <p class="text-center text-text-muted py-8">{{ __('No invoices yet') }}</p>
        @endif
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-h3 font-heading">{{ __('Recent Transactions') }}</h2>
            <a href="{{ route('cabinet.billing.transactions') }}" class="text-brand hover:underline text-body-s">{{ __('View All') }} &rarr;</a>
        </div>
        @if($recentTransactions->count())
            <div class="space-y-3">
                @foreach($recentTransactions as $tx)
                <div class="flex justify-between items-center p-3 bg-bg-soft rounded-btn">
                    <div>
                        <div class="font-semibold text-body-m">{{ $tx->description }}</div>
                        <div class="text-body-s text-text-muted">{{ $tx->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold {{ $tx->amount >= 0 ? 'text-success' : 'text-error' }}">
                            {{ $tx->amount >= 0 ? '+' : '' }}{{ number_format($tx->amount, 0, '', ' ') }} {{ __('UZS') }}
                        </div>
                        <span class="badge {{ $tx->getTypeBadgeClass() }}">{{ $tx->getTypeLabel() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-text-muted py-8">{{ __('No transactions yet') }}</p>
        @endif
    </div>
</div>

<!-- SellerMind Status -->
<div class="card">
    <h2 class="text-h3 font-heading mb-4">{{ __('SellerMind Integration') }}</h2>
    @if($sellermindLink && $sellermindLink->isActive())
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 rounded-full bg-success"></div>
            <span class="font-semibold text-success">{{ __('Connected') }}</span>
            <span class="text-text-muted">|</span>
            <span class="text-body-s text-text-muted">{{ __('Company ID') }}: {{ $sellermindLink->sellermind_company_id }}</span>
            <a href="{{ route('cabinet.sellermind.index') }}" class="ml-auto text-brand hover:underline text-body-s">{{ __('Manage') }} &rarr;</a>
        </div>
    @elseif($sellermindLink && $sellermindLink->status === 'pending')
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 rounded-full bg-warning animate-pulse"></div>
            <span class="font-semibold text-warning">{{ __('Pending') }}</span>
            <a href="{{ route('cabinet.sellermind.index') }}" class="ml-auto text-brand hover:underline text-body-s">{{ __('Complete Setup') }} &rarr;</a>
        </div>
    @else
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 rounded-full bg-gray-400"></div>
            <span class="text-text-muted">{{ __('Not connected') }}</span>
            <a href="{{ route('cabinet.sellermind.index') }}" class="ml-auto text-brand hover:underline text-body-s">{{ __('Connect') }} &rarr;</a>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('billingChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: '{{ __("Storage") }}',
                        data: @json($chartData['storage']),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: '{{ __("Shipments") }}',
                        data: @json($chartData['shipments']),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: '{{ __("Receiving") }}',
                        data: @json($chartData['receiving']),
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: '{{ __("Returns") }}',
                        data: @json($chartData['returns']),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': ' +
                                    new Intl.NumberFormat('ru-RU').format(ctx.raw) + ' UZS';
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                return new Intl.NumberFormat('ru-RU', {notation: 'compact'}).format(v);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
