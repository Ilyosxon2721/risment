@extends('manager.layout')

@section('title', 'Панель менеджера')

@section('content')
<div class="mb-8">
    <h2 class="text-h2 font-heading">Панель менеджера</h2>
    <p class="text-text-muted mt-1">{{ $company->name }}</p>
</div>

<!-- Main Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-card border border-brand-border p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-body-s text-text-muted">Ожидают подтверждения</div>
                <div class="text-h2 font-heading mt-2 {{ $pendingCount > 0 ? 'text-warning' : 'text-success' }}">{{ $pendingCount }}</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-warning/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-body-s text-text-muted">Задач за месяц</div>
                <div class="text-h2 font-heading mt-2">{{ $tasksThisMonth }}</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-brand/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-body-s text-text-muted">Начислено за месяц</div>
                <div class="text-h3 font-heading mt-2">{{ number_format($billedThisMonth, 0, '', ' ') }}</div>
                <div class="text-body-xs text-text-muted">UZS</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-body-s text-text-muted">SKU на складе</div>
                <div class="text-h2 font-heading mt-2">{{ number_format($inventoryStats['total_skus']) }}</div>
                @if($inventoryStats['low_stock'] > 0)
                    <div class="text-body-xs text-warning">{{ $inventoryStats['low_stock'] }} с низким остатком</div>
                @endif
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-card border border-brand-border p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
        </div>
        <div>
            <div class="text-body-s text-text-muted">Активные отгрузки</div>
            <div class="text-h4 font-semibold">{{ $activeShipments }}</div>
        </div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <div>
            <div class="text-body-s text-text-muted">Ожидают приёмки</div>
            <div class="text-h4 font-semibold">{{ $pendingInbounds }}</div>
        </div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
        </div>
        <div>
            <div class="text-body-s text-text-muted">Единиц на складе</div>
            <div class="text-h4 font-semibold">{{ number_format($inventoryStats['total_units']) }}</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Billing Trend Chart -->
    <div class="bg-white rounded-card border border-brand-border p-6">
        <h3 class="text-h4 font-heading mb-4">Биллинг за 6 месяцев</h3>
        <div class="h-48">
            <canvas id="billingTrendChart"></canvas>
        </div>
    </div>

    <!-- Tasks by Type Chart -->
    <div class="bg-white rounded-card border border-brand-border p-6">
        <h3 class="text-h4 font-heading mb-4">Задачи за месяц по типам</h3>
        @if(array_sum($tasksByType) > 0)
        <div class="h-48">
            <canvas id="tasksByTypeChart"></canvas>
        </div>
        @else
        <div class="h-48 flex items-center justify-center text-text-muted">
            Нет данных за текущий месяц
        </div>
        @endif
    </div>
</div>

<!-- Billing by Scope -->
@if(array_sum($billingByScope) > 0)
<div class="bg-white rounded-card border border-brand-border p-6 mb-8">
    <h3 class="text-h4 font-heading mb-4">Начисления по категориям (текущий месяц)</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @php
            $scopeLabels = [
                'inbound' => 'Приёмка',
                'pickpack' => 'Сборка',
                'shipping' => 'Доставка',
                'storage' => 'Хранение',
                'returns' => 'Возвраты',
                'packaging' => 'Упаковка',
                'labeling' => 'Маркировка',
                'photo' => 'Фото',
                'inventory' => 'Инвентаризация',
                'other' => 'Другое',
            ];
            $scopeColors = [
                'inbound' => 'blue',
                'pickpack' => 'purple',
                'shipping' => 'green',
                'storage' => 'yellow',
                'returns' => 'red',
                'packaging' => 'indigo',
                'labeling' => 'pink',
                'photo' => 'cyan',
                'inventory' => 'orange',
                'other' => 'gray',
            ];
        @endphp
        @foreach($billingByScope as $scope => $amount)
        <div class="text-center p-3 rounded-lg bg-{{ $scopeColors[$scope] ?? 'gray' }}-50">
            <div class="text-body-xs text-text-muted">{{ $scopeLabels[$scope] ?? $scope }}</div>
            <div class="text-h4 font-semibold text-{{ $scopeColors[$scope] ?? 'gray' }}-600">{{ number_format($amount, 0, '', ' ') }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="flex gap-4 mb-8 flex-wrap">
    <a href="{{ route('manager.tasks.create') }}" class="btn-brand px-6 py-3 rounded-btn text-white font-semibold">
        + Добавить задачу
    </a>
    @if($pendingCount > 0)
    <a href="{{ route('manager.confirmations.index') }}" class="btn-outline px-6 py-3 rounded-btn border-2 border-brand text-brand font-semibold">
        Подтверждения ({{ $pendingCount }})
    </a>
    @endif
    <a href="{{ route('manager.inventory.index') }}" class="px-6 py-3 rounded-btn border border-brand-border hover:bg-bg-soft font-semibold">
        Инвентарь
    </a>
    <a href="{{ route('manager.shipments.index') }}" class="px-6 py-3 rounded-btn border border-brand-border hover:bg-bg-soft font-semibold">
        Отгрузки
    </a>
</div>

<!-- Recent Tasks -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="p-6 border-b border-brand-border flex justify-between items-center">
        <h3 class="text-h4 font-heading">Последние задачи</h3>
        <a href="{{ route('manager.tasks.index') }}" class="text-brand hover:underline text-body-s">Все задачи →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Дата</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Тип</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Источник</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Статус</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Начислено</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($recentTasks as $task)
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s">{{ $task->task_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $task->task_type_label }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $task->source_label }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                            {{ $task->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-body-s font-semibold">{{ number_format($task->total_billed, 0, '', ' ') }} UZS</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('manager.tasks.show', $task) }}" class="text-brand hover:underline text-body-s">Подробнее</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-text-muted">Задач пока нет</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Billing Trend Chart
const billingTrendCtx = document.getElementById('billingTrendChart');
if (billingTrendCtx) {
    new Chart(billingTrendCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($billingTrend, 'month')) !!},
            datasets: [{
                label: 'Начисления',
                data: {!! json_encode(array_column($billingTrend, 'amount')) !!},
                backgroundColor: 'rgba(203, 79, 228, 0.5)',
                borderColor: 'rgb(203, 79, 228)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' UZS';
                        }
                    }
                }
            }
        }
    });
}

// Tasks by Type Chart
@if(array_sum($tasksByType) > 0)
const tasksByTypeCtx = document.getElementById('tasksByTypeChart');
if (tasksByTypeCtx) {
    const taskTypeLabels = @json(\App\Models\ManagerTask::getTaskTypes());
    new Chart(tasksByTypeCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys({!! json_encode($tasksByType) !!}).map(key => taskTypeLabels[key] || key),
            datasets: [{
                data: Object.values({!! json_encode($tasksByType) !!}),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(147, 51, 234, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(20, 184, 166, 0.8)',
                    'rgba(249, 115, 22, 0.8)',
                    'rgba(107, 114, 128, 0.8)',
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { boxWidth: 12 }
                }
            }
        }
    });
}
@endif
</script>
@endpush
@endsection
