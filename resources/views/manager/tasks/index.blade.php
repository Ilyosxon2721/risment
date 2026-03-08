@extends('manager.layout')

@section('title', __('Tasks'))

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-h2 font-heading">{{ __('Tasks') }}</h2>
    <a href="{{ route('manager.tasks.create') }}" class="btn-brand px-6 py-3 rounded-btn text-white font-semibold">
        {{ __('+ Add task') }}
    </a>
</div>

<!-- Filters -->
<div class="flex gap-4 mb-6">
    <form method="GET" class="flex gap-4 flex-wrap">
        <select name="task_type" onchange="this.form.submit()" class="input">
            <option value="">{{ __('All types') }}</option>
            @foreach(\App\Models\ManagerTask::getTaskTypes() as $value => $label)
                <option value="{{ $value }}" {{ request('task_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="input">
            <option value="">{{ __('All statuses') }}</option>
            <option value="pending_confirmation" {{ request('status') === 'pending_confirmation' ? 'selected' : '' }}>{{ __('Pending') }}</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
        </select>
    </form>
</div>

<!-- Tasks Table -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Type') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Source') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Charged') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Comment') }}</th>
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
                        <a href="{{ route('manager.tasks.show', $task) }}" class="text-brand hover:underline text-body-s">{{ __('Details') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-text-muted">{{ __('No tasks') }}</td>
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
