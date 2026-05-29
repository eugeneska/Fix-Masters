<?php

namespace App\Enums;

enum LeadSource: string
{
    case HeaderCallback = 'header_callback';
    case HeroDiagnostic = 'hero_diagnostic';
    case HeroMaster = 'hero_master';
    case CardLaptop = 'card_laptop';
    case CardPc = 'card_pc';
    case CardTv = 'card_tv';
    case ServicesSurvey = 'services_survey';
    case FooterForm = 'footer_form';
    case Fab = 'fab';
    case Popup = 'popup';
    case QuizContact = 'quiz_contact';

    public function label(): string
    {
        return match ($this) {
            self::HeaderCallback => 'Кнопка в хедере',
            self::HeroDiagnostic => 'Главный баннер — Заказать диагностику',
            self::HeroMaster => 'Главный баннер — Вызвать мастера',
            self::CardLaptop => 'Карточка Ноутбук',
            self::CardPc => 'Карточка ПК',
            self::CardTv => 'Карточка ТВ',
            self::ServicesSurvey => 'Популярные услуги — Пройти опрос',
            self::FooterForm => 'Форма в футере',
            self::Fab => 'Плавающая кнопка',
            self::Popup => 'Поп-ап',
            self::QuizContact => 'Квиз — контактная форма',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
