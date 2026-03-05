@extends('manager.layout')

@section('title', 'Отгрузки')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-h2 font-heading">Отгрузки клиента</h2>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">Всего</div>
        <div class="text-h3 font-heading mt-1">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">Ожидают</div>
        <div class="text-h3 font-heading mt-1 text-warning">{{ number_format($stats['pending']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">В работе</div>
        <div class="text-h3 font-heading mt-1 text-brand">{{ number_format($stats['in_progress']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">Отправлено</div>
        <div class="text-h3 font-heading mt-1 text-success">{{ number_format($stats['shipped']) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="flex gap-4 mb-6 flex-wrap">
    <form method="GET" class="flex gap-4 flex-wrap">
        <select name="status" onchange="this.form.submit()" class="input">
            <option value="">Все статусы</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Отправлен</option>
            <option value="picking" {{ request('status') === 'picking' ? 'selected' : '' }}>Сборка</option>
            <option value="packed" {{ request('status') === 'packed' ? 'selected' : '' }}>Собран</option>
            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Отправлен</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Доставлен</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Отменён</option>
        </select>
        <select name="marketplace" onchange="this.form.submit()" class="input">
            <option value="">Все маркетплейсы</option>
            <option value="uzum" {{ request('marketplace') === 'uzum' ? 'selected' : '' }}>Uzum</option>
            <option value="wb" {{ request('marketplace') === 'wb' ? 'selected' : '' }}>Wildberries</option>
            <option value="ozon" {{ request('marketplace') === 'ozon' ? 'selected' : '' }}>Ozon</option>
            <option value="yandex" {{ request('marketplace') === 'yandex' ? 'selected' : '' }}>Яндекс</option>
        </select>
    </form>
</div>

<!-- Shipments Table -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">#</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Дата</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Маркетплейс</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Склад</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Товаров</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Статус</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($shipments as $shipment)
                @php
                    $statusColors = [
                        'draft' => 'gray',
                        'submitted' => 'yellow',
                        'picking' => 'blue',
                        'packed' => 'purple',
                        'shipped' => 'green',
                        'delivered' => 'green',
                        'cancelled' => 'red',
                    ];
                    $statusLabels = [
                        'draft' => 'Черновик',
                        'submitted' => 'Отправлен',
                        'picking' => 'Сборка',
                        'packed' => 'Собран',
                        'shipped' => 'Отправлен',
                        'delivered' => 'Доставлен',
                        'cancelled' => 'Отменён',
                    ];
                @endphp
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s font-mono">#{{ $shipment->id }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $shipment->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <span class="uppercase text-body-s font-semibold">{{ $shipment->marketplace }}</span>
                    </td>
                    <td class="px-6 py-4 text-body-s">{{ $shipment->warehouse_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $shipment->items->sum('qty') }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $statusColors[$shipment->status] ?? 'gray' }}-100 text-{{ $statusColors[$shipment->status] ?? 'gray' }}-800">
                            {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('manager.shipments.show', $shipment) }}" class="text-brand hover:underline text-body-s">Подробнее</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-text-muted">Отгрузок нет</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($shipments->hasPages())
    <div class="p-4 border-t border-brand-border">
        {{ $shipments->links() }}
    </div>
    @endif
</div>
@endsection
