@extends('cabinet.layout')

@section('title', __('Saved Calculations'))

@section('content')
<div class="mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-h1 font-heading">{{ __('Saved Calculations') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Your saved calculator results') }}</p>
        </div>
        <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn btn-primary min-h-[44px] w-full sm:w-auto text-center">
            {{ __('Open Calculator') }}
        </a>
    </div>
</div>

@if($calculations->isEmpty())
<div class="card text-center py-12">
    <svg class="w-16 h-16 text-text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
    </svg>
    <h3 class="text-h3 font-heading text-text-muted mb-2">{{ __('No saved calculations') }}</h3>
    <p class="text-body-m text-text-muted mb-6">{{ __('Use the calculator and save results to see them here') }}</p>
    <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
        {{ __('Open Calculator') }}
    </a>
</div>
@else
<div class="card">
    <div class="table-responsive relative">
        <table class="w-full responsive-table">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Shipments') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Recommended plan') }}</th>
                    <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Total') }}</th>
                    <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calculations as $calc)
                @php
                    $data = $calc->calculation_data;
                    $totalShipments = ($data['micro_count'] ?? 0) + ($data['mgt_count'] ?? 0) + ($data['sgt_count'] ?? 0) + ($data['kgt_count'] ?? 0);
                @endphp
                <tr class="border-t border-brand-border hover:bg-bg-soft transition">
                    <td class="px-4 py-3 text-body-s" data-label="{{ __('Date') }}">{{ $calc->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-4 py-3 font-semibold" data-label="{{ __('Name') }}">{{ $calc->name ?: '—' }}</td>
                    <td class="px-4 py-3 text-body-s" data-label="{{ __('Shipments') }}">
                        {{ $totalShipments }} {{ __('pcs') }}
                        @php
                            $parts = [];
                            if (($data['micro_count'] ?? 0) > 0) $parts[] = $data['micro_count'] . ' MICRO';
                            if (($data['mgt_count'] ?? 0) > 0) $parts[] = $data['mgt_count'] . ' ' . __('MGT');
                            if (($data['sgt_count'] ?? 0) > 0) $parts[] = $data['sgt_count'] . ' ' . __('SGT');
                            if (($data['kgt_count'] ?? 0) > 0) $parts[] = $data['kgt_count'] . ' ' . __('LGT');
                        @endphp
                        @if(count($parts) > 0)
                            <span class="text-text-muted">({{ implode(', ', $parts) }})</span>
                        @endif
                    </td>
                    <td class="px-4 py-3" data-label="{{ __('Recommended plan') }}">
                        <span class="badge badge-info">{{ $calc->recommended_plan ?: __('Per-unit rate') }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-brand" data-label="{{ __('Total') }}">
                        {{ number_format($calc->total_cost, 0, '', ' ') }} {{ __('UZS') }}
                    </td>
                    <td class="px-4 py-3 text-right" data-label="{{ __('Actions') }}">
                        <form method="POST" action="{{ route('cabinet.calculations.destroy', ['calculation' => $calc]) }}"
                              onsubmit="return confirm('{{ __('Are you sure you want to delete this calculation?') }}')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-error hover:underline text-body-s min-h-[44px]">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($calculations->hasPages())
    <div class="px-4 py-4 border-t border-brand-border">
        {{ $calculations->links() }}
    </div>
    @endif
</div>
@endif
@endsection
