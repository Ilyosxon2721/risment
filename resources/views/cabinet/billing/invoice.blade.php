@extends('cabinet.layout')

@section('title', __('Invoice') . ' ' . $billingInvoice->invoice_number)

@section('content')
<div class="mb-8">
    <a href="{{ route('cabinet.billing.report') }}" class="text-brand hover:underline text-body-s">&larr; {{ __('Back to Billing') }}</a>
    <h1 class="text-h1 font-heading mt-4">{{ __('Invoice') }} {{ $billingInvoice->invoice_number }}</h1>
</div>

<div class="card mb-8">
    <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
        <div>
            <div class="text-body-s text-text-muted">{{ __('Period') }}</div>
            <div class="text-h4 font-heading">
                {{ $billingInvoice->period_start->format('d.m.Y') }} &mdash; {{ $billingInvoice->period_end->format('d.m.Y') }}
            </div>
        </div>
        <div class="sm:text-right">
            <span class="badge {{ $billingInvoice->getStatusBadgeClass() }}">{{ $billingInvoice->getStatusLabel() }}</span>
            <div class="text-body-s text-text-muted mt-2">{{ __('Issued') }}: {{ $billingInvoice->issue_date->format('d.m.Y') }}</div>
            <div class="text-body-s text-text-muted">{{ __('Due') }}: {{ $billingInvoice->due_date->format('d.m.Y') }}</div>
        </div>
    </div>

    <!-- Invoice Lines -->
    <div class="table-responsive relative">
    <table class="w-full responsive-table">
        <thead>
            <tr class="border-b border-brand-border text-left text-body-s text-text-muted">
                <th class="pb-3">{{ __('Service') }}</th>
                <th class="pb-3">{{ __('Description') }}</th>
                <th class="pb-3 text-right">{{ __('Qty') }}</th>
                <th class="pb-3 text-right">{{ __('Unit Price') }}</th>
                <th class="pb-3 text-right">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billingInvoice->lines as $line)
            <tr class="border-b border-brand-border/50">
                <td class="py-3" data-label="{{ __('Service') }}">
                    <span class="badge badge-secondary">{{ $line->getServiceTypeLabel() }}</span>
                </td>
                <td class="py-3" data-label="{{ __('Description') }}">{{ $line->description }}</td>
                <td class="py-3 text-right" data-label="{{ __('Qty') }}">{{ number_format($line->quantity) }}</td>
                <td class="py-3 text-right" data-label="{{ __('Unit Price') }}">{{ number_format($line->unit_price, 0, '', ' ') }}</td>
                <td class="py-3 text-right font-semibold" data-label="{{ __('Total') }}">{{ number_format($line->total_price, 0, '', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-brand-border">
                <td colspan="4" class="py-4 text-right font-heading text-h4">{{ __('Total') }}</td>
                <td class="py-4 text-right font-heading text-h3 text-brand">
                    {{ number_format($billingInvoice->total, 0, '', ' ') }} {{ __('UZS') }}
                </td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
@endsection
