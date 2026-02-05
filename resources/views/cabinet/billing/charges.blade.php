@extends('cabinet.layout')

@section('title', __('Начисления'))

@section('content')
<div class="mb-8">
    <a href="{{ route('cabinet.billing.report') }}" class="text-brand hover:underline text-body-s">&larr; {{ __('Back to Billing') }}</a>
    <h1 class="text-h1 font-heading mt-4">{{ __('Начисления за доп. услуги') }}</h1>
</div>

{{-- Filters --}}
<div class="card mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-body-s text-text-muted mb-1">{{ __('Период') }}</label>
            <select name="period" class="form-select" onchange="this.form.submit()">
                @foreach($periods as $value => $label)
                    <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-body-s text-text-muted mb-1">{{ __('Категория') }}</label>
            <select name="scope" class="form-select" onchange="this.form.submit()">
                <option value="all">{{ __('Все категории') }}</option>
                @foreach($scopeOptions as $value => $label)
                    <option value="{{ $value }}" {{ $scopeFilter === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- Summary by Scope --}}
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    @foreach($summary['by_scope'] as $scope => $data)
        <div class="card text-center {{ $data['total'] > 0 ? '' : 'opacity-50' }}">
            <div class="text-body-s text-text-muted mb-1">{{ $data['label'] }}</div>
            <div class="text-h3 font-semibold {{ $data['total'] > 0 ? 'text-brand' : '' }}">
                {{ number_format($data['total'], 0, '', ' ') }}
            </div>
            <div class="text-body-xs text-text-muted">{{ __('сум') }}</div>
        </div>
    @endforeach
</div>

{{-- Grand Total --}}
<div class="card mb-6 bg-brand/5 border-brand">
    <div class="flex justify-between items-center">
        <span class="text-h3">{{ __('Итого за период') }}:</span>
        <span class="text-h2 font-bold text-brand">{{ number_format($summary['grand_total'], 0, '', ' ') }} {{ __('сум') }}</span>
    </div>
</div>

{{-- Detailed Items Table --}}
<div class="card">
    <h2 class="text-h3 font-heading mb-4">{{ __('Детализация') }}</h2>

    @if($items->count())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-brand-border text-left text-body-s text-text-muted">
                        <th class="pb-3">{{ __('Дата') }}</th>
                        <th class="pb-3">{{ __('Категория') }}</th>
                        <th class="pb-3">{{ __('Услуга') }}</th>
                        <th class="pb-3">{{ __('Статус') }}</th>
                        <th class="pb-3 text-right">{{ __('Цена') }}</th>
                        <th class="pb-3 text-right">{{ __('Кол-во') }}</th>
                        <th class="pb-3 text-right">{{ __('Сумма') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="border-b border-brand-border/50 hover:bg-gray-50">
                        <td class="py-3 text-body-s">{{ $item->occurred_at ? $item->occurred_at->format('d.m.Y H:i') : ($item->billed_at ? $item->billed_at->format('d.m.Y H:i') : '-') }}</td>
                        <td class="py-3">
                            <span class="badge badge-{{ $item->scope === 'inbound' ? 'info' : ($item->scope === 'pickpack' ? 'success' : ($item->scope === 'storage' ? 'warning' : ($item->scope === 'shipping' ? 'primary' : ($item->scope === 'returns' ? 'danger' : 'gray')))) }}">
                                {{ $scopeOptions[$item->scope] ?? $item->scope }}
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="font-medium">{{ $item->getTitle() }}</div>
                            @if($item->comment)
                                <div class="text-body-xs text-text-muted">{{ $item->comment }}</div>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($item->status === 'invoiced')
                                <span class="badge badge-success">{{ __('В счёте') }}</span>
                            @elseif($item->status === 'accrued')
                                <span class="badge badge-warning">{{ __('Начислено') }}</span>
                            @else
                                <span class="badge badge-gray">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td class="py-3 text-right text-body-s">{{ number_format($item->unit_price, 0, '', ' ') }}</td>
                        <td class="py-3 text-right text-body-s">{{ number_format($item->qty, $item->qty == intval($item->qty) ? 0 : 2, '.', ' ') }}</td>
                        <td class="py-3 text-right font-semibold">{{ number_format($item->amount, 0, '', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-brand-border font-semibold">
                        <td colspan="6" class="py-3 text-right">{{ __('Итого на странице') }}:</td>
                        <td class="py-3 text-right text-brand">{{ number_format($items->sum('amount'), 0, '', ' ') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6">
            {{ $items->links() }}
        </div>
    @else
        <p class="text-center text-text-muted py-8">{{ __('Нет начислений за выбранный период') }}</p>
    @endif
</div>
@endsection
