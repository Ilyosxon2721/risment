@extends('manager.layout')

@section('title', 'Задача #' . $task->id)

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('manager.tasks.index') }}" class="text-brand hover:underline">&larr; Назад</a>
    <h2 class="text-h2 font-heading">Задача #{{ $task->id }}</h2>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
        {{ $task->status_label }}
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Task Info -->
    <div class="bg-white rounded-card border border-brand-border p-6">
        <h3 class="text-h4 font-heading mb-4">Информация</h3>
        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-text-muted">Тип:</dt>
                <dd class="font-semibold">{{ $task->task_type_label }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-text-muted">Источник:</dt>
                <dd class="font-semibold">{{ $task->source_label }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-text-muted">Дата выполнения:</dt>
                <dd class="font-semibold">{{ $task->task_date->format('d.m.Y') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-text-muted">Создал:</dt>
                <dd class="font-semibold">{{ $task->creator->name ?? '-' }}</dd>
            </div>
            @if($task->confirmed_at)
            <div class="flex justify-between">
                <dt class="text-text-muted">Подтверждено:</dt>
                <dd class="font-semibold">{{ $task->confirmed_at->format('d.m.Y H:i') }}</dd>
            </div>
            @endif
            @if($task->confirmer)
            <div class="flex justify-between">
                <dt class="text-text-muted">Подтвердил:</dt>
                <dd class="font-semibold">{{ $task->confirmer->name }}</dd>
            </div>
            @endif
        </dl>

        @if($task->comment)
        <div class="mt-4 pt-4 border-t border-brand-border">
            <div class="text-text-muted text-body-s mb-1">Комментарий:</div>
            <p>{{ $task->comment }}</p>
        </div>
        @endif

        @if($task->details)
        <div class="mt-4 pt-4 border-t border-brand-border">
            <div class="text-text-muted text-body-s mb-1">Детали:</div>
            <div class="text-body-s bg-bg-soft rounded p-3 font-mono">
                @foreach($task->details as $key => $value)
                    <div><span class="text-text-muted">{{ $key }}:</span> {{ $value }}</div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Billing Items -->
    <div class="bg-white rounded-card border border-brand-border p-6">
        <h3 class="text-h4 font-heading mb-4">Начисления</h3>
        @if($task->billingItems->isNotEmpty())
            <div class="space-y-3">
                @foreach($task->billingItems as $item)
                <div class="flex justify-between items-center p-3 bg-bg-soft rounded-card">
                    <div>
                        <div class="font-semibold text-body-s">{{ $item->title_ru }}</div>
                        <div class="text-text-muted text-body-s">{{ $item->scope }} &middot; {{ number_format($item->unit_price, 0, '', ' ') }} x {{ $item->qty }}</div>
                    </div>
                    <div class="font-semibold">{{ number_format($item->amount, 0, '', ' ') }} UZS</div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-brand-border flex justify-between">
                <span class="font-semibold">Итого:</span>
                <span class="text-h4 font-heading">{{ number_format($task->total_billed, 0, '', ' ') }} UZS</span>
            </div>
        @else
            <p class="text-text-muted">Начислений нет</p>
        @endif
    </div>
</div>
@endsection
