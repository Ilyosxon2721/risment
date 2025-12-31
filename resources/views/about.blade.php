@extends('layouts.app')

@section('title', __('About') . ' - RISMENT')

@section('content')
<section class="gradient-brand text-white py-20">
    <div class="container-risment text-center">
        <h1 class="text-h1 font-heading mb-6">
            @if(app()->getLocale() === 'ru')
                О компании RISMENT
            @else
                RISMENT kompaniyasi haqida
            @endif
        </h1>
        <p class="text-body-l max-w-3xl mx-auto opacity-90">
            @if(app()->getLocale() === 'ru')
                Профессиональный фулфилмент для маркетплейсов Узбекистана
            @else
                O'zbekiston marketplace'lari uchun professional fulfillment
            @endif
        </p>
    </div>
</section>

<section class="py-16">
    <div class="container-risment max-w-4xl">
        <div class="prose max-w-none mb-12">
            <h2 class="text-h2 font-heading mb-6">
                @if(app()->getLocale() === 'ru')
                    Миссия
                @else
                    Missiya
                @endif
            </h2>
            <p class="text-body-m text-text-muted mb-6">
                @if(app()->getLocale() === 'ru')
                    RISMENT — это современный фулфилмент-центр, специализирующийся на обслуживании продавцов на маркетплейсах Узбекистана. Мы помогаем бизнесу масштабироваться, беря на себя все логистические задачи: от хранения товаров до их упаковки и доставки.
                @else
                    RISMENT - bu O'zbekiston marketplace'larida sotuvchilarga xizmat ko'rsatishga ixtisoslashgan zamonaviy fulfillment markaz. Biz biznesni kengaytirishga yordam beramiz, barcha logistika vazifalarini o'z zimmamizga olamiz: mahsulotlarni saqlashdan ularni qadoqlash va yetkazish gacha.
                @endif
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="card text-center">
                <div class="text-4xl font-bold text-brand mb-2">1000+</div>
                <div class="text-body-s text-text-muted">
                    @if(app()->getLocale() === 'ru')
                        Заказов в день
                    @else
                        Kunlik buyurtmalar
                    @endif
                </div>
            </div>
            <div class="card text-center">
                <div class="text-4xl font-bold text-brand mb-2">99.9%</div>
                <div class="text-body-s text-text-muted">
                    @if(app()->getLocale() === 'ru')
                        Точность упаковки
                    @else
                        Qadoqlash aniqligi
                    @endif
                </div>
            </div>
            <div class="card text-center">
                <div class="text-4xl font-bold text-brand mb-2">24/7</div>
                <div class="text-body-s text-text-muted">
                    @if(app()->getLocale() === 'ru')
                        Поддержка
                    @else
                        Qo'llab-quvvatlash
                    @endif
                </div>
            </div>
        </div>
        
        <div class="mb-12">
            <h2 class="text-h2 font-heading mb-6">
                @if(app()->getLocale() === 'ru')
                    Наши преимущества
                @else
                    Bizning afzalliklarimiz
                @endif
            </h2>
            <div class="space-y-4">
                <div class="card">
                    <h3 class="text-h4 font-heading mb-2">
                        @if(app()->getLocale() === 'ru')
                            Прозрачность
                        @else
                            Shaffoflik
                        @endif
                    </h3>
                    <p class="text-body-m text-text-muted">
                        @if(app()->getLocale() === 'ru')
                            Личный кабинет с доступом к данным в режиме реального времени
                        @else
                            Real vaqt rejimida ma'lumotlarga kirish imkoniyati bilan shaxsiy kabinet
                        @endif
                    </p>
                </div>
                <div class="card">
                    <h3 class="text-h4 font-heading mb-2">
                        @if(app()->getLocale() === 'ru')
                            Надёжность
                        @else
                            Ishonchlilik
                        @endif
                    </h3>
                    <p class="text-body-m text-text-muted">
                        @if(app()->getLocale() === 'ru')
                            SLA на каждый процесс с финансовыми гарантиями
                        @else
                            Moliyaviy kafolatlar bilan har bir jarayon uchun SLA
                        @endif
                    </p>
                </div>
                <div class="card">
                    <h3 class="text-h4 font-heading mb-2">
                        @if(app()->getLocale() === 'ru')
                            Технологичность
                        @else
                            Texnologiya
                        @endif
                    </h3>
                    <p class="text-body-m text-text-muted">
                        @if(app()->getLocale() === 'ru')
                            Современное складское оборудование и WMS система
                        @else
                            Zamonaviy ombor uskunalari va WMS tizimi
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="card gradient-brand text-white text-center p-8">
            <h2 class="text-h2 font-heading mb-4">
                @if(app()->getLocale() === 'ru')
                    Готовы начать работу?
                @else
                    Ishni boshlashga tayyormisiz?
                @endif
            </h2>
            <p class="text-body-l mb-6 opacity-90">
                @if(app()->getLocale() === 'ru')
                    Свяжитесь с нами для консультации
                @else
                    Maslaha uchun biz bilan bog'laning
                @endif
            </p>
            <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-primary bg-white text-brand">
                {{ __('Contacts') }}
            </a>
        </div>
    </div>
</section>
@endsection
