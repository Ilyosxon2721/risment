@extends('layouts.app')

@section('title', __('Contacts') . ' - RISMENT')

@section('content')
<section class="py-16">
    <div class="container-risment max-w-6xl">
        <h1 class="text-h1 font-heading text-center mb-12">{{ __('Contacts') }}</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div class="space-y-8">
                <div class="card">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-brand/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-h4 font-heading mb-2">{{ __('Phone') }}</h3>
                            <p class="text-body-m text-text-muted">+998 (90) 123-45-67</p>
                            <p class="text-body-m text-text-muted">+998 (91) 234-56-78</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-brand/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-h4 font-heading mb-2">Email</h3>
                            <p class="text-body-m text-text-muted">info@risment.uz</p>
                            <p class="text-body-m text-text-muted">support@risment.uz</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-brand/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-h4 font-heading mb-2">
                                @if(app()->getLocale() === 'ru')
                                    Адрес
                                @else
                                    Manzil
                                @endif
                            </h3>
                            <p class="text-body-m text-text-muted">
                                @if(app()->getLocale() === 'ru')
                                    г. Ташкент, Узбекистан
                                @else
                                    Toshkent, O'zbekiston
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <h3 class="text-h4 font-heading mb-4">
                        @if(app()->getLocale() === 'ru')
                            Режим работы
                        @else
                            Ish vaqti
                        @endif
                    </h3>
                    <p class="text-body-m text-text-muted mb-2">
                        @if(app()->getLocale() === 'ru')
                            Понедельник - Пятница: 9:00 - 18:00
                        @else
                            Dushanba - Juma: 9:00 - 18:00
                        @endif
                    </p>
                    <p class="text-body-m text-text-muted">
                        @if(app()->getLocale() === 'ru')
                            Суббота - Воскресенье: Выходной
                        @else
                            Shanba - Yakshanba: Dam olish
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="card">
                <h2 class="text-h3 font-heading mb-6">
                    @if(app()->getLocale() === 'ru')
                        Оставить заявку
                    @else
                        So'rov qoldirish
                    @endif
                </h2>
                
                {{-- Success Message --}}
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-btn">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
                @endif
                
                {{-- Error Messages --}}
                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-btn">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-red-800 font-medium mb-2">{{ __('Please correct the following errors:') }}</p>
                            <ul class="list-disc list-inside text-red-700 text-body-s">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <form action="{{ route('lead.store', ['locale' => app()->getLocale()]) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-body-s font-semibold mb-2">{{ __('Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="input @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-500 text-body-s mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-body-s font-semibold mb-2">{{ __('Phone') }} <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" class="input @error('phone') border-red-500 @enderror" placeholder="+998 (__) ___-__-__" value="{{ old('phone') }}" required>
                        @error('phone')
                            <p class="text-red-500 text-body-s mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-body-s font-semibold mb-2">{{ __('Company') }}</label>
                        <input type="text" name="company_name" class="input @error('company_name') border-red-500 @enderror" value="{{ old('company_name') }}">
                        @error('company_name')
                            <p class="text-red-500 text-body-s mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-body-s font-semibold mb-2">
                            @if(app()->getLocale() === 'ru')
                                Комментарий
                            @else
                                Izoh
                            @endif
                        </label>
                        <textarea name="comment" class="input @error('comment') border-red-500 @enderror" rows="4">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-body-s mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('Submit') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
