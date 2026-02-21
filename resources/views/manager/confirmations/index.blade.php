@extends('manager.layout')

@section('title', 'Подтверждения')

@section('content')
<div class="mb-8">
    <h2 class="text-h2 font-heading">Подтверждения из SellerMind</h2>
    <p class="text-text-muted mt-1">Задачи требующие подтверждения менеджером</p>
</div>

<div class="bg-white rounded-card border border-brand-border">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Дата</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Тип</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Детали</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">Создан</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($tasks as $task)
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s">{{ $task->task_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-body-s">{{ $task->task_type_label }}</td>
                    <td class="px-6 py-4 text-body-s">
                        @if($task->details)
                            @if(isset($task->details['sellermind_order_id']))
                                Заказ SM#{{ $task->details['sellermind_order_id'] }}
                            @endif
                            @if(isset($task->details['marketplace']))
                                ({{ $task->details['marketplace'] }})
                            @endif
                            @if(isset($task->details['items_count']))
                                — {{ $task->details['items_count'] }} позиций
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-s text-text-muted">{{ $task->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <form method="POST" action="{{ route('manager.confirmations.confirm', $task) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-btn hover:bg-green-700 font-semibold" onclick="return confirm('Подтвердить задачу и начислить биллинг?')">
                                    Подтвердить
                                </button>
                            </form>
                            <form method="POST" action="{{ route('manager.confirmations.reject', $task) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 text-sm rounded-btn hover:bg-red-200 font-semibold" onclick="return confirm('Отклонить задачу?')">
                                    Отклонить
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-text-muted">
                        Нет задач ожидающих подтверждения
                    </td>
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
