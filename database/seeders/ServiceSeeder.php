<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // UZUM - FBS
            [
                'scheme' => 'fbs',
                'marketplace' => 'uzum',
                'slug' => 'uzum-fbs',
                'title_ru' => 'Uzum FBS - Fulfillment by Seller',
                'title_uz' => 'Uzum FBS - Sotuvchi tomonidan bajarilish',
                'content_ru' => '<h3>Что такое FBS для Uzum?</h3>
<p>FBS (Fulfillment by Seller) - схема работы, при которой мы храним ваш товар на нашем складе и собираем заказы, а доставку до клиента осуществляет курьер маркетплейса Uzum.</p>

<h4>Как это работает:</h4>
<ul>
<li>Вы привозите товар на наш склад</li>
<li>Мы принимаем, маркируем и размещаем товар</li>
<li>При поступлении заказа с Uzum мы собираем и упаковываем товар</li>
<li>Курьер Uzum забирает заказ и доставляет клиенту</li>
</ul>

<h4>Преимущества:</h4>
<ul>
<li>Быстрая обработка заказов (в день поступления)</li>
<li>Профессиональная упаковка</li>
<li>Фотофиксация каждого этапа</li>
<li>SLA гарантии на каждый процесс</li>
</ul>

<h4>Стоимость:</h4>
<p>Логистика: от 5,000 UZS в зависимости от размера (MGT/SGT/KGT)</p>
<p>Pick & Pack: 7,000 UZS за первую позицию + 1,200 UZS за каждую дополнительную</p>',
                'content_uz' => '<h3>Uzum uchun FBS nima?</h3>
<p>FBS (Fulfillment by Seller) - biz sizning mahsulotingizni omborimizda saqlaydigan va buyurtmalarni yig\'amiz, Uzum kuryer xizmati esa mijozga yetkazib beradi.</p>

<h4>Qanday ishlaydi:</h4>
<ul>
<li>Mahsulotni bizning omborimizga olib kelasiz</li>
<li>Biz qabul qilamiz, belgila</li>
<li>Uzumdan buyurtma kelganda yig\'amiz va qadoqlaymiz</li>
<li>Uzum kuryer buyurtmani oladi va mijozga yetkazadi</li>
</ul>

<h4>Afzalliklar:</h4>
<ul>
<li>Tez ishlov berish (buyurtma kunida)</li>
<li>Professional qadoqlash</li>
<li>Har bir bosqichning fotosuratlari</li>
<li>Har bir jarayon uchun SLA kafolati</li>
</ul>',
                'is_active' => true,
                'sort' => 1,
            ],
            
            // UZUM - FBO
            [
                'scheme' => 'fbo',
                'marketplace' => 'uzum',
                'slug' => 'uzum-fbo',
                'title_ru' => 'Uzum FBO - Поставки на склад маркетплейса',
                'title_uz' => 'Uzum FBO - Marketplace omboriga yetkazish',
                'content_ru' => '<h3>Uzum FBO - Fulfillment by Operator</h3>
<p>FBO - схема, при которой мы формируем поставки товара на склад Uzum. Маркетплейс самостоятельно хранит, собирает и доставляет заказы.</p>

<h4>Что мы делаем:</h4>
<ul>
<li>Принимаем ваш товар на наш склад</li>
<li>Формируем поставки согласно требованиям Uzum</li>
<li>Упаковываем в короба 60×40×40 см</li>
<li>Маркируем каждую единицу товара</li>
<li>Доставляем на склад Uzum</li>
</ul>

<h4>Преимущества FBO:</h4>
<ul>
<li>Быстрая доставка клиентам (Uzum берет на себя)</li>
<li>Меньше возвратов благодаря контролю качества</li>
<li>Участие в акциях и быстрой доставке Uzum</li>
</ul>

<h4>Стоимость:</h4>
<p>Доставка на склад Uzum: 50,000 UZS за короб 60×40×40 см</p>',
                'content_uz' => '<h3>Uzum FBO - Marketplace omboriga yetkazish</h3>
<p>FBO - biz Uzum omboriga mahsulot yetkazib beramiz. Marketplace o\'zi saqlaydi, yig\'adi va yetkazib beradi.</p>

<h4>Biz nima qilamiz:</h4>
<ul>
<li>Mahsulotni omborimizga qabul qilamiz</li>
<li>Uzum talablariga muvofiq yetkazish shakllantiramiz</li>
<li>60×40×40 sm qutilarga qadoqlaymiz</li>
<li>Har bir mahsulotga belgi qo\'yamiz</li>
<li>Uzum omboriga yetkazamiz</li>
</ul>',
                'is_active' => true,
                'sort' => 2,
            ],
            
            // Wildberries - FBS
            [
                'scheme' => 'fbs',
                'marketplace' => 'wb',
                'slug' => 'wildberries-fbs',
                'title_ru' => 'Wildberries FBS',
                'title_uz' => 'Wildberries FBS',
                'content_ru' => '<h3>Wildberries FBS</h3>
<p>Работаем по схеме FBS для Wildberries - храним, собираем и передаем заказы курьерам WB.</p>

<h4>Особенности работы с WB:</h4>
<ul>
<li>Строгие требования к упаковке и маркировке</li>
<li>Обязательная фотофиксация при передаче курьеру</li>
<li>Контроль SLA по времени сборки</li>
<li>Работа с системой EDBS (экспресс-доставка)</li>
</ul>

<h4>Наши услуги:</h4>
<ul>
<li>Приемка и размещение товара</li>
<li>Сборка заказов по требованиям WB</li>
<li>Упаковка (стрейч, пупырка, fragile)</li>
<li>Передача курьеру с актом</li>
</ul>',
                'content_uz' => '<h3>Wildberries FBS</h3>
<p>Wildberries uchun FBS sxemasida ishlaymiz - saqlaymiz, yig\'amiz va WB kur\'erlariga topshiramiz.</p>',
                'is_active' => true,
                'sort' => 3,
            ],
            
            // Wildberries - FBO
            [
                'scheme' => 'fbo',
                'marketplace' => 'wb',
                'slug' => 'wildberries-fbo',
                'title_ru' => 'Wildberries FBO',
                'title_uz' => 'Wildberries FBO',
                'content_ru' => '<h3>Wildberries FBO</h3>
<p>Формируем поставки на склады Wildberries по всей России и СНГ.</p>

<h4>Что входит:</h4>
<ul>
<li>Подготовка товара по стандартам WB</li>
<li>Короба 60×40×40 см с маркировкой</li>
<li>Формирование поставки в системе WB</li>
<li>Доставка на указанный склад WB</li>
</ul>

<p>Стоимость: 50,000 UZS за короб</p>',
                'content_uz' => '<h3>Wildberries FBO</h3>
<p>Rossiya va MDH bo\'ylab Wildberries omborlariga yetkazish shakllantiramiz.</p>',
                'is_active' => true,
                'sort' => 4,
            ],
            
            // Ozon - FBS
            [
                'scheme' => 'fbs',
                'marketplace' => 'ozon',
                'slug' => 'ozon-fbs',
                'title_ru' => 'Ozon FBS',
                'title_uz' => 'Ozon FBS',
                'content_ru' => '<h3>Ozon FBS</h3>
<p>Fulfillment для Ozon - храним товар и передаем собранные заказы курьерам Ozon.</p>

<h4>Работа с Ozon:</h4>
<ul>
<li>Интеграция с Ozon Seller</li>
<li>Автоматическое получение заказов</li>
<li>Сборка по требованиям Ozon</li>
<li>Передача с QR-кодами</li>
</ul>',
                'content_uz' => '<h3>Ozon FBS</h3>
<p>Ozon uchun fulfillment - mahsulotni saqlaymiz va yig\'ilgan buyurtmalarni Ozon kur\'erlariga topshiramiz.</p>',
                'is_active' => true,
                'sort' => 5,
            ],
            
            // Ozon - FBO
            [
                'scheme' => 'fbo',
                'marketplace' => 'ozon',
                'slug' => 'ozon-fbo',
                'title_ru' => 'Ozon FBO',
                'title_uz' => 'Ozon FBO',
                'content_ru' => '<h3>Ozon FBO</h3>
<p>Поставки товара на склады Ozon.</p>

<h4>Процесс:</h4>
<ul>
<li>Приемка товара</li>
<li>Подготовка по стандартам Ozon</li>
<li>Упаковка в короба</li>
<li>Создание поставки в Ozon Seller</li>
<li>Доставка на склад Ozon</li>
</ul>',
                'content_uz' => '<h3>Ozon FBO</h3>
<p>Ozon omborlariga mahsulot yetkazish.</p>',
                'is_active' => true,
                'sort' => 6,
            ],
            
            // Yandex Market - FBS
            [
                'scheme' => 'fbs',
                'marketplace' => 'yandex',
                'slug' => 'yandex-fbs',
                'title_ru' => 'Yandex Market FBS',
                'title_uz' => 'Yandex Market FBS',
                'content_ru' => '<h3>Yandex Market FBS</h3>
<p>Храним товар и собираем заказы для Yandex Market.</p>

<h4>Особенности:</h4>
<ul>
<li>Работа через Яндекс.Доставку</li>
<li>Сборка заказов по стандартам Yandex</li>
<li>Упаковка с защитой</li>
</ul>',
                'content_uz' => '<h3>Yandex Market FBS</h3>
<p>Mahsulotni saqlaymiz va Yandex Market uchun buyurtmalarni yig\'amiz.</p>',
                'is_active' => true,
                'sort' => 7,
            ],
            
            // Yandex Market - FBO
            [
                'scheme' => 'fbo',
                'marketplace' => 'yandex',
                'slug' => 'yandex-fbo',
                'title_ru' => 'Yandex Market FBO',
                'title_uz' => 'Yandex Market FBO',
                'content_ru' => '<h3>Yandex Market FBO</h3>
<p>Поставки на склады Yandex Market.</p>',
                'content_uz' => '<h3>Yandex Market FBO</h3>
<p>Yandex Market omborlariga yetkazish.</p>',
                'is_active' => true,
                'sort' => 8,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
