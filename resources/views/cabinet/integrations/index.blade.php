@extends('cabinet.layout')

@section('title', __('integrations.title'))

@section('content')
<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('integrations.title') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('integrations.subtitle') }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($integrations as $integration)
        <div class="card flex flex-col justify-between {{ $integration['available'] ? '' : 'opacity-70' }}">
            <div>
                <!-- Icon + Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-btn bg-bg-soft flex items-center justify-center">
                        @if($integration['icon'] === 'link')
                            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        @elseif($integration['icon'] === 'database')
                            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                        @elseif($integration['icon'] === 'archive')
                            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        @endif
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-body-s font-medium px-2.5 py-1 rounded-full
                        @if($integration['status'] === 'connected')
                            bg-success/10 text-success
                        @elseif($integration['status'] === 'pending')
                            bg-warning/10 text-warning
                        @elseif($integration['status'] === 'coming_soon')
                            bg-bg-soft text-text-muted
                        @else
                            bg-bg-soft text-text-muted
                        @endif
                    ">
                        @if($integration['status'] === 'connected')
                            <span class="w-2 h-2 rounded-full bg-success"></span>
                            {{ __('integrations.status_connected') }}
                        @elseif($integration['status'] === 'pending')
                            <span class="w-2 h-2 rounded-full bg-warning animate-pulse"></span>
                            {{ __('integrations.status_pending') }}
                        @elseif($integration['status'] === 'coming_soon')
                            {{ __('integrations.status_coming_soon') }}
                        @else
                            {{ __('integrations.status_disconnected') }}
                        @endif
                    </span>
                </div>

                <!-- Name + Description -->
                <h3 class="text-h3 font-heading mb-2">{{ $integration['name'] }}</h3>
                <p class="text-body-s text-text-muted mb-6">{{ $integration['description'] }}</p>
            </div>

            <!-- Action Button -->
            <div>
                @if($integration['available'])
                    <a href="{{ $integration['route'] }}" class="btn {{ $integration['status'] === 'connected' ? 'btn-outline' : 'btn-primary' }} w-full text-center">
                        @if($integration['status'] === 'connected' || $integration['status'] === 'pending')
                            {{ __('integrations.configure') }}
                        @else
                            {{ __('integrations.connect') }}
                        @endif
                    </a>
                @else
                    <button disabled class="btn btn-outline w-full opacity-50 cursor-not-allowed">
                        {{ __('integrations.status_coming_soon') }}
                    </button>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
