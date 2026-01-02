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
                        
                        @if($message->attachments->count() > 0)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="text-body-s font-semibold text-text-muted mb-2">{{ __('Attachments') }}:</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($message->attachments as $attachment)
                                <a href="{{ asset('storage/' . $attachment->path) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-bg-soft rounded-btn hover:bg-gray-200 transition text-body-s">
                                    @if($attachment->is_image)
                                    <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    @endif
                                    <span>{{ $attachment->original_name }}</span>
                                    <span class="text-text-muted">({{ $attachment->formatted_size }})</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
            <div class="border-t border-brand-border pt-6">
                <form action="{{ route('cabinet.tickets.reply', ['ticket' => $ticket]) }}" method="POST" enctype="multipart/form-data">
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
                    
                    <div class="mb-4">
                        <label for="reply_attachments" class="block text-body-m font-semibold text-brand-dark mb-2">
                            {{ __('Attachments') }}
                        </label>
                        <input 
                            type="file" 
                            id="reply_attachments" 
                            name="attachments[]" 
                            multiple
                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                            class="block w-full text-body-s text-text-muted
                                   file:mr-4 file:py-2 file:px-3
                                   file:rounded-btn file:border-0
                                   file:bg-brand file:text-white
                                   hover:file:bg-brand-dark
                                   cursor-pointer"
                        >
                        <p class="text-body-s text-text-muted mt-1">{{ __('Max 5 files, 10MB each') }}</p>
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
