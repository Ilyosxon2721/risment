@extends('manager.layout')

@section('title', 'Отгрузка #' . $shipment->id)

@section('content')
<div class="mb-8">
    <a href="{{ route('manager.shipments.index') }}" class="text-brand hover:underline text-body-s">&larr; Назад к списку</a>
    <h2 class="text-h2 font-heading mt-4">Отгрузка #{{ $shipment->id }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-card border border-brand-border p-6 mb-6">
            <h3 class="text-h4 font-heading mb-4">Информация</h3>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-body-s text-text-muted">Маркетплейс</dt>
                    <dd class="font-semibold uppercase">{{ $shipment->marketplace }}</dd>
                </div>
                <div>
                    <dt class="text-body-s text-text-muted">Склад</dt>
                    <dd class="font-semibold">{{ $shipment->warehouse_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-body-s text-text-muted">Дата создания</dt>
                    <dd>{{ $shipment->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-body-s text-text-muted">Плановая дата</dt>
                    <dd>{{ $shipment->planned_at ? $shipment->planned_at->format('d.m.Y') : '-' }}</dd>
                </div>
            </dl>
            @if($shipment->notes)
            <div class="mt-4 pt-4 border-t border-brand-border">
                <dt class="text-body-s text-text-muted">Примечания</dt>
                <dd class="mt-1">{{ $shipment->notes }}</dd>
            </div>
            @endif
        </div>

        <!-- Items -->
        <div class="bg-white rounded-card border border-brand-border p-6">
            <h3 class="text-h4 font-heading mb-4">Товары</h3>
            <table class="w-full">
                <thead class="bg-bg-soft">
                    <tr>
                        <th class="px-4 py-2 text-left text-body-s font-semibold text-text-muted">SKU</th>
                        <th class="px-4 py-2 text-left text-body-s font-semibold text-text-muted">Название</th>
                        <th class="px-4 py-2 text-right text-body-s font-semibold text-text-muted">Количество</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach($shipment->items as $item)
                    <tr>
                        <td class="px-4 py-3 font-mono text-body-s">{{ $item->sku->sku ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->sku->name ?? 'Без названия' }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ $item->qty }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-brand-border font-semibold">
                        <td colspan="2" class="px-4 py-3 text-right">Итого:</td>
                        <td class="px-4 py-3 text-right">{{ $shipment->items->sum('qty') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Status Panel -->
    <div>
        <div class="bg-white rounded-card border border-brand-border p-6">
            <h3 class="text-h4 font-heading mb-4">Статус</h3>

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

            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-semibold bg-{{ $statusColors[$shipment->status] ?? 'gray' }}-100 text-{{ $statusColors[$shipment->status] ?? 'gray' }}-800">
                    {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                </span>
            </div>

            <form method="POST" action="{{ route('manager.shipments.status', $shipment) }}">
                @csrf
                <label class="block text-body-s font-semibold mb-2">Изменить статус</label>
                <select name="status" class="input w-full mb-4">
                    <option value="draft" {{ $shipment->status === 'draft' ? 'selected' : '' }}>Черновик</option>
                    <option value="submitted" {{ $shipment->status === 'submitted' ? 'selected' : '' }}>Отправлен клиентом</option>
                    <option value="picking" {{ $shipment->status === 'picking' ? 'selected' : '' }}>На сборке</option>
                    <option value="packed" {{ $shipment->status === 'packed' ? 'selected' : '' }}>Собран</option>
                    <option value="shipped" {{ $shipment->status === 'shipped' ? 'selected' : '' }}>Отправлен</option>
                    <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Доставлен</option>
                    <option value="cancelled" {{ $shipment->status === 'cancelled' ? 'selected' : '' }}>Отменён</option>
                </select>
                <button type="submit" class="btn-brand w-full px-4 py-3 rounded-btn text-white font-semibold">
                    Обновить статус
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
