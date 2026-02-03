@extends('cabinet.layout')

@section('title', __('integrations.sellermind_page_title'))

@section('content')
<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('cabinet.integrations.index') }}" class="inline-flex items-center gap-2 text-body-m text-text-muted hover:text-brand transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('integrations.back_to_integrations') }}
    </a>
</div>

<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('integrations.sellermind_page_title') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('integrations.sellermind_page_subtitle') }}</p>
</div>

@if($link && $link->isActive())
    <!-- Active Connection -->
    <div class="card mb-8 border-2 border-success">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-3 h-3 rounded-full bg-success animate-pulse"></div>
                    <h2 class="text-h3 font-heading text-success">{{ __('integrations.sellermind_connected') }}</h2>
                </div>
                <div class="space-y-2 text-body-m">
                    <p><span class="text-text-muted">{{ __('integrations.sellermind_company_id') }}:</span> <strong>{{ $link->sellermind_company_id }}</strong></p>
                    <p><span class="text-text-muted">{{ __('integrations.sellermind_connected_at') }}:</span> <strong>{{ $link->linked_at?->format('d.m.Y H:i') }}</strong></p>
                </div>
            </div>
            <form method="POST" action="{{ route('cabinet.integrations.sellermind.disconnect') }}" onsubmit="return confirm('{{ __('integrations.sellermind_confirm_disconnect') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline text-error border-error hover:bg-error hover:text-white">
                    {{ __('integrations.disconnect') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Sync Settings -->
    <div class="card mb-8">
        <h2 class="text-h3 font-heading mb-6">{{ __('integrations.sync_settings') }}</h2>
        <form method="POST" action="{{ route('cabinet.integrations.sellermind.settings') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <label class="flex items-center gap-3 p-4 bg-bg-soft rounded-btn cursor-pointer">
                    <input type="hidden" name="sync_products" value="0">
                    <input type="checkbox" name="sync_products" value="1" {{ $link->sync_products ? 'checked' : '' }} class="w-5 h-5 text-brand rounded">
                    <div>
                        <div class="font-semibold">{{ __('integrations.sync_products') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('integrations.sync_products_desc') }}</div>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-bg-soft rounded-btn cursor-pointer">
                    <input type="hidden" name="sync_orders" value="0">
                    <input type="checkbox" name="sync_orders" value="1" {{ $link->sync_orders ? 'checked' : '' }} class="w-5 h-5 text-brand rounded">
                    <div>
                        <div class="font-semibold">{{ __('integrations.sync_orders') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('integrations.sync_orders_desc') }}</div>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-bg-soft rounded-btn cursor-pointer">
                    <input type="hidden" name="sync_stock" value="0">
                    <input type="checkbox" name="sync_stock" value="1" {{ $link->sync_stock ? 'checked' : '' }} class="w-5 h-5 text-brand rounded">
                    <div>
                        <div class="font-semibold">{{ __('integrations.sync_stock') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('integrations.sync_stock_desc') }}</div>
                    </div>
                </label>
            </div>
            <div class="mt-6">
                <button type="submit" class="btn btn-primary">{{ __('integrations.save_settings') }}</button>
            </div>
        </form>
    </div>

@elseif($link && $link->status === 'pending')
    <!-- Pending Connection — Token Display -->
    <div class="card mb-8 border-2 border-warning" id="token-section">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-3 h-3 rounded-full bg-warning animate-pulse"></div>
            <h2 class="text-h3 font-heading text-warning">{{ __('integrations.status_pending') }}</h2>
        </div>

        <p class="text-body-m mb-4">{{ __('integrations.token_instruction') }}</p>

        <!-- Token Field -->
        <div class="relative">
            <div class="flex items-stretch gap-0 border-2 border-brand rounded-btn overflow-hidden {{ session('newToken') ? 'ring-2 ring-brand ring-offset-2' : '' }}">
                <input
                    type="text"
                    id="link-token"
                    value="{{ $link->link_token }}"
                    readonly
                    class="flex-1 px-4 py-4 font-mono text-lg bg-bg-soft border-0 focus:outline-none select-all tracking-wider"
                >
                <button
                    type="button"
                    onclick="copyToken()"
                    id="copy-btn"
                    class="px-6 bg-brand text-white font-semibold hover:bg-brand/90 transition-colors flex items-center gap-2 shrink-0"
                >
                    <svg id="copy-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg id="check-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="copy-text">{{ __('integrations.copy') }}</span>
                </button>
            </div>
        </div>

        <!-- Step-by-step Instructions -->
        <div class="mt-6 p-4 bg-bg-soft rounded-btn">
            <h3 class="font-semibold mb-3">{{ __('integrations.how_to_connect') }}</h3>
            <ol class="space-y-2 text-body-m text-text-muted list-decimal list-inside">
                <li>{{ __('integrations.step_1') }}</li>
                <li>{{ __('integrations.step_2') }}</li>
                <li>{{ __('integrations.step_3') }}</li>
                <li>{{ __('integrations.step_4') }}</li>
            </ol>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <form method="POST" action="{{ route('cabinet.integrations.sellermind.disconnect') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline text-text-muted">{{ __('integrations.cancel') }}</button>
            </form>
            <form method="POST" action="{{ route('cabinet.integrations.sellermind.generate') }}">
                @csrf
                <button type="submit" class="btn btn-outline">{{ __('integrations.regenerate_token') }}</button>
            </form>
        </div>
    </div>

@else
    <!-- No Connection -->
    <div class="card mb-8">
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto mb-6 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <h2 class="text-h3 font-heading mb-2">{{ __('integrations.not_connected_title') }}</h2>
            <p class="text-body-m text-text-muted mb-6">
                {{ __('integrations.not_connected_desc') }}
            </p>
            <form method="POST" action="{{ route('cabinet.integrations.sellermind.generate') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">{{ __('integrations.generate_token') }}</button>
            </form>
        </div>
    </div>

    <!-- Benefits -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card text-center">
            <svg class="w-10 h-10 mx-auto mb-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="text-h4 font-heading mb-2">{{ __('integrations.benefit_products') }}</h3>
            <p class="text-body-s text-text-muted">{{ __('integrations.benefit_products_desc') }}</p>
        </div>
        <div class="card text-center">
            <svg class="w-10 h-10 mx-auto mb-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-h4 font-heading mb-2">{{ __('integrations.benefit_orders') }}</h3>
            <p class="text-body-s text-text-muted">{{ __('integrations.benefit_orders_desc') }}</p>
        </div>
        <div class="card text-center">
            <svg class="w-10 h-10 mx-auto mb-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <h3 class="text-h4 font-heading mb-2">{{ __('integrations.benefit_stock') }}</h3>
            <p class="text-body-s text-text-muted">{{ __('integrations.benefit_stock_desc') }}</p>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
function copyToken() {
    const input = document.getElementById('link-token');
    const copyIcon = document.getElementById('copy-icon');
    const checkIcon = document.getElementById('check-icon');
    const copyText = document.getElementById('copy-text');
    const copyBtn = document.getElementById('copy-btn');

    navigator.clipboard.writeText(input.value).then(function() {
        copyIcon.classList.add('hidden');
        checkIcon.classList.remove('hidden');
        copyText.textContent = '{{ __("integrations.copied") }}';
        copyBtn.classList.add('bg-success');
        copyBtn.classList.remove('bg-brand');

        setTimeout(function() {
            copyIcon.classList.remove('hidden');
            checkIcon.classList.add('hidden');
            copyText.textContent = '{{ __("integrations.copy") }}';
            copyBtn.classList.remove('bg-success');
            copyBtn.classList.add('bg-brand');
        }, 2000);
    }).catch(function() {
        input.select();
        document.execCommand('copy');
        copyText.textContent = '{{ __("integrations.copied") }}';
        setTimeout(function() {
            copyText.textContent = '{{ __("integrations.copy") }}';
        }, 2000);
    });
}

@if(session('newToken'))
document.addEventListener('DOMContentLoaded', function() {
    const section = document.getElementById('token-section');
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
@endif
</script>
@endpush
