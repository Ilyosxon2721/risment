@extends('manager.layout')

@section('title', 'Панель менеджера')

@section('content')
<div class="mb-8">
    <h2 class="text-h2 font-heading">Панель менеджера</h2>
    <p class="text-text-muted mt-1">{{ $company->name }}</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-card border border-brand-border p-6">
        <div class="text-body-s text-text-muted">Ожидают подтверждения</div>
        <div class="text-h2 font-heading mt-2 {{ $pendingCount > 0 ? 'text-warning' : 'text-success' }}">{{ $pendingCount }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-6">
        <div class="text-body-s text-text-muted">Задач за месяц</div>
        <div class="text-h2 font-heading mt-2">{{ $tasksThisMonth }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-6">
        <div class="text-body-s text-text-muted">Начислено за месяц</div>
        <div class="text-h2 font-heading mt-2">{{ number_format($billedThisMonth, 0, '', ' ') }} UZS</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="flex gap-4 mb-8">
    <a href="{{ route('manager.tasks.create') }}" class="btn-brand px-6 py-3 rounded-btn text-white font-semibold">
        + Добавить задачу
    </a>
    @if($pendingCount > 0)
    <a href="{{ route('manager.confirmations.index') }}" class="btn-outline px-6 py-3 rounded-btn border border-brand font-semibold">
        Подтверждения ({{ $pendingCount }})
    </a>
    @endif
</div>

<!-- Recent Tasks -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="p-6 border-b border-brand-border">
        <h3 class="text-h4 font-heading">Последние задачи</h3>
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
@endsection
