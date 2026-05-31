<?php

namespace App\Enums;

enum LeadTrafficChannel: string
{
    case GoogleAds = 'google_ads';
    case YandexAds = 'yandex_ads';
    case Social = 'social';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GoogleAds => 'Google Реклама',
            self::YandexAds => 'Яндекс Реклама',
            self::Social => 'Соцсети',
            self::Other => 'Органика / прочее',
        };
    }

    /**
     * Куда отправлять событие «Качественный лид» при отметке менеджером.
     *
     * Google Ads, соцсети, SEO, прямой заход и прочее → GA4 + Метрика.
     * Яндекс Реклама → только Метрика.
     */
    public function conversionTarget(): string
    {
        return match ($this) {
            self::YandexAds => 'metrika',
            self::GoogleAds, self::Social, self::Other => 'both',
        };
    }
}
