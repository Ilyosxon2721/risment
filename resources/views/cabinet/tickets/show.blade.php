@extends('cabinet.layout')

@section('title', __('Ticket') . ' #' . $ticket->id)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Ticket') }} #{{ $ticket->id }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ $ticket->subject }}</p>
        </div>
        <a href="{{ route('cabinet.tickets.index') }}" class="btn btn-ghost">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Messages -->
    <div class="lg:col-span-2">
        <div class="card mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-h3 font-heading">{{ __('Conversation') }}</h2>
                <div class="flex gap-2">
                    <span class="badge badge-{{ 
                        $ticket->priority === 'low' ? 'info' : 
                        ($ticket->priority === 'high' ? 'error' : 'warning') 
                    }}">
                        {{ __(ucfirst($ticket->priority)) }}
                    </span>
                    <span class="badge badge-{{ 
                        $ticket->status === 'closed' ? 'secondary' : 
                        ($ticket->status === 'open' ? 'success' : 'warning') 
                    }}">
                        {{ __(ucfirst($ticket->status)) }}
                    </span>
                </div>
            </div>
            
            <!-- Messages Thread -->
            <div class="space-y-4 mb-6">
                @foreach($ticket->messages as $message)
                <div class="flex gap-4 {{ $message->is_internal ? 'bg-blue-50' : '' }} p-4 rounded-btn">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center font-semibold">
                            {{ substr($message->user->name, 0, 1) }}
                        </div>
                    </div>
                    
                    <!-- Message Content -->
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-semibold">{{ $message->user->name }}</span>
                            @if($message->is_internal)
                            <span class="badge badge-info badge-sm">{{ __('Support') }}</span>
                            @endif
                            <span class="text-body-s text-text-muted">{{ $message->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="text-body-m whitespace-pre-line">{{ $message->message }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
            <div class="border-t border-brand-border pt-6">
                <form action="{{ route('cabinet.tickets.messages', ['ticket' => $ticket]) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="message" class="block text-body-m font-semibold text-brand-dark mb-2">
                            {{ __('Reply') }}
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="4" 
                            class="input w-full @error('message') border-error @enderror"
                            placeholder="{{ __('Type your message...') }}"
                            required
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-error text-body-s mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Send Message') }}
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="border-t border-brand-border pt-6 text-center text-text-muted">
                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>{{ __('This ticket is closed') }}</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="card sticky top-4">
            <h3 class="text-h4 font-heading mb-4">{{ __('Ticket Information') }}</h3>
            
            <div class="space-y-4 text-body-s">
                <div>
                    <div class="text-text-muted mb-1">{{ __('Created') }}</div>
                    <div class="font-semibold">{{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                </div>
                
                <div>
                    <div class="text-text-muted mb-1">{{ __('Status') }}</div>
                    <div>
                        <span class="badge badge-{{ 
                            $ticket->status === 'closed' ? 'secondary' : 
                            ($ticket->status === 'open' ? 'success' : 'warning') 
                        }}">
                            {{ __(ucfirst($ticket->status)) }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <div class="text-text-muted mb-1">{{ __('Priority') }}</div>
                    <div>
                        <span class="badge badge-{{ 
                            $ticket->priority === 'low' ? 'info' : 
                            ($ticket->priority === 'high' ? 'error' : 'warning') 
                        }}">
                            {{ __(ucfirst($ticket->priority)) }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <div class="text-text-muted mb-1">{{ __('Messages') }}</div>
                    <div class="font-semibold">{{ $ticket->messages->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
