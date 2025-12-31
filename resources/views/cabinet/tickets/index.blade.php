@extends('cabinet.layout')

@section('title', __('Tickets'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Tickets') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Get help from support team') }}</p>
        </div>
        <a href="{{ route('cabinet.tickets.create') }}" class="btn btn-primary">
            + {{ __('Create Ticket') }}
        </a>
    </div>
</div>

@if($tickets->isEmpty())
<div class="card text-center py-12">
    <svg class="w-16 h-16 text-text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
    <h3 class="text-h3 font-heading text-text-muted mb-2">{{ __('No tickets yet') }}</h3>
    <p class="text-body-m text-text-muted mb-6">{{ __('Create a ticket to get help from our support team') }}</p>
    <a href="{{ route('cabinet.tickets.create') }}" class="btn btn-primary">
        + {{ __('Create Ticket') }}
    </a>
</div>
@else
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">ID</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Subject') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Priority') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Created') }}</th>
                    <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr class="border-t border-brand-border hover:bg-bg-soft transition">
                    <td class="px-4 py-3 font-mono text-body-s">#{{ $ticket->id }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $ticket->subject }}</td>
                    <td class="px-4 py-3">
                        <span class="badge badge-{{ 
                            $ticket->priority === 'low' ? 'info' : 
                            ($ticket->priority === 'high' ? 'error' : 'warning') 
                        }}">
                            {{ __(ucfirst($ticket->priority)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge badge-{{ 
                            $ticket->status === 'closed' ? 'secondary' : 
                            ($ticket->status === 'open' ? 'success' : 'warning') 
                        }}">
                            {{ __(ucfirst($ticket->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('cabinet.tickets.show', ['ticket' => $ticket]) }}" 
                           class="text-brand hover:underline text-body-s">
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($tickets->hasPages())
    <div class="px-4 py-4 border-t border-brand-border">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endif
@endsection
