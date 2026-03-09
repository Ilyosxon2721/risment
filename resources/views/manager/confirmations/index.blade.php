@extends('manager.layout')

@section('title', __('Confirmations'))

@section('content')
<div class="mb-8">
    <h2 class="text-xl sm:text-h2 font-heading">{{ __('Confirmations from SellerMind') }}</h2>
    <p class="text-text-muted mt-1">{{ __('Tasks requiring manager confirmation') }}</p>
</div>

<div class="bg-white rounded-card border border-brand-border">
    <div class="table-responsive relative">
        <table class="w-full responsive-table">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Type') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Details') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Created') }}</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($tasks as $task)
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s" data-label="{{ __('Date') }}">{{ $task->task_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-body-s" data-label="{{ __('Type') }}">{{ $task->task_type_label }}</td>
                    <td class="px-6 py-4 text-body-s" data-label="{{ __('Details') }}">
                        @if($task->details)
                            @if(isset($task->details['sellermind_order_id']))
                                {{ __('Order') }} SM#{{ $task->details['sellermind_order_id'] }}
                            @endif
                            @if(isset($task->details['marketplace']))
                                ({{ $task->details['marketplace'] }})
                            @endif
                            @if(isset($task->details['items_count']))
                                — {{ $task->details['items_count'] }} {{ __('items') }}
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-s text-text-muted" data-label="{{ __('Created') }}">{{ $task->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 text-right" data-label="{{ __('Actions') }}">
                        <div class="flex justify-end gap-2">
                            <form method="POST" action="{{ route('manager.confirmations.confirm', $task) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-btn hover:bg-green-700 font-semibold min-h-[44px]" onclick="return confirm('{{ __('Confirm task and charge billing?') }}')">
                                    {{ __('Confirm') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('manager.confirmations.reject', $task) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 text-sm rounded-btn hover:bg-red-200 font-semibold min-h-[44px]" onclick="return confirm('{{ __('Reject task?') }}')">
                                    {{ __('Reject') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-text-muted">
                        {{ __('No tasks awaiting confirmation') }}
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
