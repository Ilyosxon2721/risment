@extends('manager.layout')

@section('title', 'Приёмки')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-h2 font-heading">Приёмки клиента</h2>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">Всего</div>
        <div class="text-h3 font-heading mt-1">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">Ожидают приёмки</div>
        <div class="text-h3 font-heading mt-1 text-warning">{{ number_format($stats['pending']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">Принято</div>
        <div class="text-h3 font-heading mt-1 text-success">{{ number_format($stats['received']) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="flex gap-4 mb-6">
    <form method="GET" class="flex gap-4">
        <select name="status" onchange="this.form.submit()" class="input">
            <option value="">Все статусы</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Отправлена</option>
            <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Принята</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Отменена</option>
        </select>
    </form>
</div>

<!-- Inbounds Table -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">#</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Дата</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Плановая</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Позиций</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Статус</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($inbounds as $inbound)
                @php
                    $statusColors = [
                        'draft' => 'gray',
                        'submitted' => 'yellow',
                        'received' => 'green',
                        'cancelled' => 'red',
                    ];
                    $statusLabels = [
                        'draft' => 'Черновик',
                        'submitted' => 'Ожидает',
                        'received' => 'Принята',
                        'cancelled' => 'Отменена',
                    ];
                @endphp
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s font-mono">#{{ $inbound->id }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $inbound->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $inbound->expected_at ? $inbound->expected_at->format('d.m.Y') : '-' }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $inbound->items->count() }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $statusColors[$inbound->status] ?? 'gray' }}-100 text-{{ $statusColors[$inbound->status] ?? 'gray' }}-800">
                            {{ $statusLabels[$inbound->status] ?? $inbound->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('manager.inbounds.show', $inbound) }}" class="text-brand hover:underline text-body-s">Подробнее</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-text-muted">Приёмок нет</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($inbounds->hasPages())
    <div class="p-4 border-t border-brand-border">
        {{ $inbounds->links() }}
    </div>
    @endif
</div>
@endsection
