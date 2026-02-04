@extends('cabinet.layout')

@section('title', __('marketplaces.title'))

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-h1 font-heading">{{ __('marketplaces.title') }}</h1>
        <p class="text-body-m text-text-muted mt-2">{{ __('marketplaces.subtitle') }}</p>
    </div>
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="btn btn-primary">{{ __('marketplaces.add') }}</button>
        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border rounded-card shadow-lg z-10">
            @foreach($marketplaces as $mp)
                <a href="{{ route('cabinet.marketplaces.create', ['marketplace' => $mp]) }}" class="block px-4 py-3 hover:bg-bg-soft text-body-m">
                    {{ __('marketplaces.mp_' . $mp) }}
                </a>
            @endforeach
        </div>
    </div>
</div>

@foreach($marketplaces as $mp)
    <div class="mb-8">
        <h2 class="text-h3 font-heading mb-4">{{ __('marketplaces.mp_' . $mp) }}</h2>

        @if(isset($grouped[$mp]) && $grouped[$mp]->count())
            <div class="space-y-4">
                @foreach($grouped[$mp] as $cred)
                    <div class="card flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full {{ $cred->is_active ? 'bg-success' : 'bg-text-muted' }}"></div>
                                <span class="font-semibold">{{ $cred->name }}</span>
                                @if($cred->isSyncedToSellermind())
                                    <span class="text-body-s text-success">{{ __('marketplaces.synced') }}</span>
                                @endif
                            </div>
                            <div class="text-body-s text-text-muted mt-1">
                                {{ __('marketplaces.added') }}: {{ $cred->created_at->format('d.m.Y') }}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('cabinet.marketplaces.edit', $cred) }}" class="btn btn-outline btn-sm">{{ __('marketplaces.edit') }}</a>
                            <form method="POST" action="{{ route('cabinet.marketplaces.destroy', $cred) }}" onsubmit="return confirm('{{ __('marketplaces.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm text-error border-error hover:bg-error hover:text-white">{{ __('marketplaces.delete') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card text-center text-text-muted py-6">
                {{ __('marketplaces.no_accounts') }}
            </div>
        @endif
    </div>
@endforeach
@endsection
