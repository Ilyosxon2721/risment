@extends('layouts.app')

@section('title', __('FAQ') . ' - RISMENT')

@section('content')
<!-- Hero -->
<section class="gradient-brand text-white py-16">
    <div class="container-risment">
        <h1 class="text-h1 font-heading mb-4">{{ __('FAQ') }}</h1>
        <p class="text-body-l">{{ __('Frequently Asked Questions') }}</p>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-16">
    <div class="container-risment max-w-4xl">
        @php
            $faqs_ru = [
                [
                    'q' => 'Что такое фулфилмент и чем RISMENT отличается от обычного склада?',
                    'a' => 'Фулфилмент — это не просто хранение. RISMENT принимает товар, ведёт учёт, собирает и упаковывает заказы, делает маркировку и отгружает на маркетплейсы или покупателям. Вы получаете процесс "под ключ" и прозрачные тарифы.'
                ],
                [
                    'q' => 'С какими маркетплейсами вы работаете?',
                    'a' => 'Мы работаем с Uzum Market, Wildberries, Ozon и Yandex Market. По запросу подключаем новые площадки.'
                ],
                [
                    'q' => 'Какие схемы вы поддерживаете: FBO, FBS, DBS?',
                    'a' => '<strong>FBO</strong> — вы отгружаете партии на склад маркетплейса через нас.<br><strong>FBS</strong> — мы собираем и упаковываем заказы, доставка обычно выполняется маркетплейсом.<br><strong>DBS</strong> — доставка выполняется продавцом (может быть через RISMENT по договорённости).'
                ],
                [
                    'q' => 'Что такое EDBS и почему у разных площадок это разное?',
                    'a' => 'EDBS — термин, который трактуется по-разному у разных маркетплейсов. Мы уточняем схему под конкретную площадку и фиксируем регламент в договоре, чтобы не было сюрпризов.'
                ],
                [
                    'q' => 'Как рассчитывается "мелкий/средний/крупный" товар (МГТ/СГТ/КГТ)?',
                    'a' => 'Категория определяется по сумме трёх сторон товара в упаковке: <strong>Д + Ш + В</strong> (см):<br>• <strong>МГТ:</strong> ≤ 60 см<br>• <strong>СГТ:</strong> 60–170 см<br>• <strong>КГТ:</strong> > 170 см<br>Если в заказе несколько товаров — категория считается по <strong>самому крупному</strong>.'
                ],
                [
                    'q' => 'Вы делаете маркировку и подготовку поставок на склад маркетплейса?',
                    'a' => 'Да. Мы выполняем сортировку, упаковку, маркировку товара и коробов, формируем партии и подготавливаем документы/реестры.'
                ],
                [
                    'q' => 'Делаете ли вы сборку заказов (Pick&Pack) для FBS/DBS?',
                    'a' => 'Да. Мы собираем заказ, упаковываем, при необходимости добавляем доп.защиту (хрупкое/стретч/сейф-пакет) и передаём на отгрузку по схеме.'
                ],
                [
                    'q' => 'Есть ли минимальные объёмы для начала работы?',
                    'a' => 'Минимальный объём не обязателен, но на небольших объёмах обычно выгоднее стартовать с базового пакета и понятной операционки. Менеджер поможет подобрать формат.'
                ],
                [
                    'q' => 'Как быстро вы обрабатываете заказы?',
                    'a' => 'Срок обработки зависит от нагрузки и требований к упаковке. Для большинства сценариев мы работаем в режиме "день-в-день" или "на следующий день". Конкретные SLA фиксируются в регламенте.'
                ],
                [
                    'q' => 'Можно ли привезти товар без штрихкодов/артикулов?',
                    'a' => 'Можно, но это замедляет приёмку и повышает риск ошибок. Рекомендуем поставки с баркодами и корректными названиями SKU. Если баркодов нет — мы можем помочь, но это отдельная услуга.'
                ],
                [
                    'q' => 'Как вы ведёте учёт остатков?',
                    'a' => 'Остатки ведутся по SKU/штрихкоду с привязкой к месту хранения. Клиент видит данные в личном кабинете (если подключено).'
                ],
                [
                    'q' => 'Что делать, если пришёл брак или возврат?',
                    'a' => 'Мы принимаем возврат, проводим проверку (по регламенту), делаем фотофиксацию, переупаковываем или выводим в брак/списание по согласованию.'
                ],
                [
                    'q' => 'Кто отвечает за ошибки комплектации?',
                    'a' => 'Если ошибка возникла на стороне RISMENT — мы разбираем инцидент по трекингу/фото и компенсируем по условиям договора. Если ошибка из-за неверных данных клиента (SKU, баркоды, инструкции) — ответственность фиксируется регламентом.'
                ],
                [
                    'q' => 'Вы работаете только в Коканде?',
                    'a' => 'Склад расположен в Коканде. Отгрузки на склады маркетплейсов и логистика выполняются по согласованному графику и условиям.'
                ],
                [
                    'q' => 'Как начать работу с RISMENT?',
                    'a' => 'Оставьте заявку на сайте. Мы уточним товары, схемы (FBO/FBS/DBS), объёмы и подготовим договор, тарифный план и регламент.'
                ],
            ];

            $faqs_uz = [
                [
                    'q' => 'Fulfillment nima va RISMENT oddiy ombordan nimasi bilan farq qiladi?',
                    'a' => 'Fulfillment — bu faqat saqlash emas. RISMENT tovarni qabul qiladi, hisobini yuritadi, buyurtmalarni yig\'adi va qadoqlaydi, markirovka qiladi hamda marketplace omboriga yoki mijozga jo\'natadi. Jarayon "kalit topshirish" ko\'rinishida bo\'ladi.'
                ],
                [
                    'q' => 'Qaysi marketplace\'lar bilan ishlaysiz?',
                    'a' => 'Uzum Market, Wildberries, Ozon va Yandex Market bilan ishlaymiz. So\'rov bo\'yicha boshqa platformalarni ham ulash mumkin.'
                ],
                [
                    'q' => 'Qaysi sxemalar: FBO, FBS, DBS?',
                    'a' => '<strong>FBO</strong> — partiya ko\'rinishida marketplace omboriga jo\'natish (RISMENT orqali).<br><strong>FBS</strong> — buyurtmani yig\'ish va qadoqlash, yetkazib berish odatda marketplace tomoni.<br><strong>DBS</strong> — yetkazib berishni sotuvchi bajaradi (kelishuvga ko\'ra RISMENT orqali ham bo\'lishi mumkin).'
                ],
                [
                    'q' => 'EDBS nima va nega turli platformalarda turlicha?',
                    'a' => 'EDBS atamasi marketplace\'ga qarab boshqacha ishlatiladi. Biz har bir platforma bo\'yicha sxemani aniqlab, reglamеntni shartnomada aniq yozib qo\'yamiz.'
                ],
                [
                    'q' => '"Kichik/o\'rta/katta" (MGT/SGT/KGT) qanday aniqlanadi?',
                    'a' => 'Kategoriya qadoqdagi o\'lchamlar yig\'indisi bo\'yicha: <strong>U + E + B</strong> (sm):<br>• <strong>MGT:</strong> ≤ 60 sm<br>• <strong>SGT:</strong> 60–170 sm<br>• <strong>KGT:</strong> > 170 sm<br>Agar buyurtmada bir nechta tovar bo\'lsa — kategoriya <strong>eng kattasi</strong> bo\'yicha olinadi.'
                ],
                [
                    'q' => 'Marketplace omboriga jo\'natish uchun markirovka va partiya tayyorlaysizmi?',
                    'a' => 'Ha. Saralash, qadoqlash, tovar va korobkalarni markirovka qilish, partiya shakllantirish hamda hujjatlar/reestrlarni tayyorlashni qilamiz.'
                ],
                [
                    'q' => 'FBS/DBS uchun buyurtma yig\'ish (Pick&Pack) bormi?',
                    'a' => 'Ha. Buyurtma yig\'iladi, qadoqlanadi, kerak bo\'lsa qo\'shimcha himoya (mo\'rt/strech/safe-paket) qo\'shiladi va sxema bo\'yicha jo\'natishga tayyorlanadi.'
                ],
                [
                    'q' => 'Ishni boshlash uchun minimal hajm kerakmi?',
                    'a' => 'Majburiy minimal hajm yo\'q. Lekin kichik hajmlarda bazaviy paket va aniq operatsiya bilan boshlash foydaliroq bo\'ladi. Menejer mos variantni tavsiya qiladi.'
                ],
                [
                    'q' => 'Buyurtmalarni qanchada tayyorlaysiz?',
                    'a' => 'Muddat yuklama va qadoqlash talablarga bog\'liq. Ko\'p holatda "shu kuni" yoki "ertasi kuni" rejimida ishlaymiz. Aniq SLA reglamеntda belgilanadi.'
                ],
                [
                    'q' => 'Shtrix-kodsiz tovar olib kelish mumkinmi?',
                    'a' => 'Mumkin, lekin qabul qilish sekinlashadi va xatolik riski oshadi. Shuning uchun SKU nomlari va barcode\'lar tayyor bo\'lishi tavsiya qilinadi. Barcode bo\'lmasa — alohida xizmat sifatida yordam beramiz.'
                ],
                [
                    'q' => 'Qoldiq hisobi qanday yuritiladi?',
                    'a' => 'Qoldiq SKU/shtrix-kod bo\'yicha va saqlash joyi bilan yuritiladi. Ulangan bo\'lsa, mijoz shaxsiy kabinetda ko\'radi.'
                ],
                [
                    'q' => 'Brak yoki qaytgan tovar bo\'lsa nima qilasiz?',
                    'a' => 'Qaytgan tovar qabul qilinadi, reglamеnt bo\'yicha tekshiriladi, foto-fiksatsiya qilinadi. Kelishuvga ko\'ra qayta qadoqlash yoki brak/yo\'q qilish amalga oshiriladi.'
                ],
                [
                    'q' => 'Komplektatsiya xatosi uchun kim javob beradi?',
                    'a' => 'Agar xato RISMENT tomonidan bo\'lsa — treking/foto asosida tekshiramiz va shartnoma bo\'yicha kompensatsiya qilamiz. Agar xato noto\'g\'ri ma\'lumot (SKU, barcode, ko\'rsatma) sabab bo\'lsa — javobgarlik reglamеntda belgilanadi.'
                ],
                [
                    'q' => 'Faqat Qo\'qonda ishlaysizmi?',
                    'a' => 'Ombor Qo\'qonda joylashgan. Marketplace omborlariga jo\'natish va logistika kelishilgan grafik va shartlar bo\'yicha bajariladi.'
                ],
                [
                    'q' => 'RISMENT bilan qanday boshlash mumkin?',
                    'a' => 'Saytda ariza qoldiring. Tovar, sxema (FBO/FBS/DBS), hajmni aniqlab, shartnoma, tarif va reglamеntni tayyorlaymiz.'
                ],
            ];

            $faqs = app()->getLocale() === 'ru' ? $faqs_ru : $faqs_uz;
        @endphp

        <div class="space-y-4">
            @foreach($faqs as $index => $faq)
            <div class="card">
                <button type="button" class="faq-question w-full text-left flex justify-between items-center" onclick="toggleFaq({{ $index }})">
                    <span class="font-semibold text-body-l pr-4">{{ $faq['q'] }}</span>
                    <svg class="faq-icon w-6 h-6 text-brand transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq-{{ $index }}" class="faq-answer hidden mt-4 text-body-m text-text-muted">
                    {!! $faq['a'] !!}
                </div>
            </div>
            @endforeach
        </div>

        <!-- CTA -->
        <div class="mt-12 text-center card gradient-brand text-white">
            <h3 class="text-h3 font-heading mb-4">{{ __('Still have questions?') }}</h3>
            <p class="text-body-l mb-6 opacity-90">{{ __('Contact us for detailed consultation') }}</p>
            <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn bg-white text-brand hover:bg-white/90">
                {{ __('Contact Us') }}
            </a>
        </div>
    </div>
</section>

<script>
function toggleFaq(index) {
    const answer = document.getElementById('faq-' + index);
    const icon = answer.previousElementSibling.querySelector('.faq-icon');
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        answer.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection
