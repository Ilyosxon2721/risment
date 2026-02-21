@extends('manager.layout')

@section('title', 'Задачи')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-h2 font-heading">Задачи</h2>
    <a href="{{ route('manager.tasks.create') }}" class="btn-brand px-6 py-3 rounded-btn text-white font-semibold">
        + Добавить задачу
    </a>
</div>

<!-- Filters -->
<div class="flex gap-4 mb-6">
    <form method="GET" class="flex gap-4">
        <select name="task_type" onchange="this.form.submit()" class="input">
            <option value="">Все типы</option>
            <option value="inbound" {{ request('task_type') === 'inbound' ? 'selected' : '' }}>Приёмка</option>
            <option value="pickpack" {{ request('task_type') === 'pickpack' ? 'selected' : '' }}>Сборка</option>
            <option value="delivery" {{ request('task_type') === 'delivery' ? 'selected' : '' }}>Отгрузка</option>
            <option value="storage" {{ request('task_type') === 'storage' ? 'selected' : '' }}>Хранение</option>
            <option value="return" {{ request('task_type') === 'return' ? 'selected' : '' }}>Возврат</option>
        </select>
        <select name="status" onchange="this.form.submit()" class="input">
            <option value="">Все статусы</option>
            <option value="pending_confirmation" {{ request('status') === 'pending_confirmation' ? 'selected' : '' }}>Ожидает</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Подтверждён</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Отклонён</option>
        </select>
    </form>
</div>

<!-- Tasks Table -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Дата</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Тип</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Источник</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Статус</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Начислено</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Комментарий</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($tasks as $task)
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
                    <td class="px-6 py-4 text-body-s text-text-muted">{{ \Illuminate\Support\Str::limit($task->comment, 40) }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('manager.tasks.show', $task) }}" class="text-brand hover:underline text-body-s">Подробнее</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-text-muted">Задач нет</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tasks->hasPages())
    <div class="p-4 border-t border-brand-border">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endsection
