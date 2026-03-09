@extends('cabinet.layout')

@section('title', __('Invoices'))

@section('content')
<div class="mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-h1 font-heading">{{ __('Invoices') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('All your invoices') }}</p>
        </div>
        <a href="{{ route('cabinet.finance.index') }}" class="btn btn-secondary min-h-[44px]">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
        <label for="status" class="text-body-m font-semibold">{{ __('Status') }}:</label>
        <select name="status" id="status" class="input w-full sm:w-64" onchange="this.form.submit()">
            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ __('Sent') }}</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('Overdue') }}</option>
        </select>
    </form>
</div>

<!-- Invoices Table -->
@if($invoices->count() > 0)
<div class="card overflow-hidden">
    <div class="table-responsive relative">
        <table class="table responsive-table">
            <thead>
                <tr>
                    <th>{{ __('Invoice Number') }}</th>
                    <th>{{ __('Issue Date') }}</th>
                    <th>{{ __('Due Date') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td class="font-semibold" data-label="{{ __('Invoice Number') }}">{{ $invoice->invoice_number }}</td>
                    <td data-label="{{ __('Issue Date') }}">{{ $invoice->issue_date->format('d.m.Y') }}</td>
                    <td data-label="{{ __('Due Date') }}">{{ $invoice->due_date->format('d.m.Y') }}</td>
                    <td class="font-semibold" data-label="{{ __('Amount') }}">{{ number_format($invoice->total, 0, '', ' ') }} {{ __('UZS') }}</td>
                    <td data-label="{{ __('Status') }}">
                        <span class="badge {{ $invoice->getStatusBadgeClass() }}">{{ $invoice->getStatusLabel() }}</span>
                    </td>
                    <td class="text-right" data-label="{{ __('Actions') }}">
                        <a href="{{ route('cabinet.finance.invoice', ['invoice' => $invoice->id]) }}" class="btn btn-sm btn-secondary">
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $invoices->links() }}
</div>

@else
<!-- Empty State -->
<div class="card text-center py-12">
    <svg class="w-16 h-16 text-text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <h3 class="text-h4 font-heading mb-2">{{ __('No invoices yet') }}</h3>
    <p class="text-body-m text-text-muted mb-4">{{ __('Invoices will appear here once issued') }}</p>
</div>
@endif
@endsection
