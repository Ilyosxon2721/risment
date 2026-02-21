@extends('manager.layout')

@section('title', 'Биллинг')

@section('content')
<div class="mb-8">
    <h2 class="text-h2 font-heading">Биллинг — {{ $company->name }}</h2>
    <p class="text-text-muted mt-1">Период: {{ now()->translatedFormat('F Y') }}</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @php
        $scopes = [
            'inbound' => 'Приёмка',
            'pickpack' => 'Сборка',
            'shipping' => 'Доставка',
            'storage' => 'Хранение',
            'returns' => 'Возвраты',
            'other' => 'Прочее',
        ];
    @endphp
    @foreach($scopes as $scope => $label)
        <div class="bg-white rounded-card border border-brand-border p-4">
            <div class="text-body-s text-text-muted">{{ $label }}</div>
            <div class="text-h4 font-heading mt-1">
                {{ number_format($summary['totals_by_scope'][$scope] ?? 0, 0, '', ' ') }}
            </div>
            <div class="text-body-s text-text-muted">UZS</div>
        </div>
    @endforeach
</div>

<!-- Grand Total -->
<div class="bg-white rounded-card border border-brand-border p-6 mb-8 flex justify-between items-center">
    <div>
        <div class="text-text-muted">Итого за период</div>
        <div class="text-body-s text-text-muted">{{ $summary['item_count'] }} начислений</div>
    </div>
    <div class="text-h2 font-heading">{{ number_format($summary['grand_total'], 0, '', ' ') }} UZS</div>
</div>

<!-- Recent Items -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="p-6 border-b border-brand-border">
        <h3 class="text-h4 font-heading">Последние начисления</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Дата</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Описание</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Раздел</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">Цена</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">Кол-во</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">Сумма</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($recentItems as $item)
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s">{{ $item->created_at->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $item->title_ru }}</td>
                    <td class="px-6 py-4 text-body-s">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $scopes[$item->scope] ?? $item->scope }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-body-s text-right">{{ number_format($item->unit_price, 0, '', ' ') }}</td>
                    <td class="px-6 py-4 text-body-s text-right">{{ $item->qty }}</td>
                    <td class="px-6 py-4 text-body-s text-right font-semibold">{{ number_format($item->amount, 0, '', ' ') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-text-muted">Начислений за текущий период нет</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
