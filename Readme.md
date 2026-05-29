# FIX-MASTERS

Сайт сервиса ремонта техники (Минск) на **Laravel 13**. Страницы отдаются через Blade, статика — из `public/`.

## Требования

- PHP 8.3+
- Composer
- SQLite (по умолчанию в `.env`) или MySQL/PostgreSQL

## Запуск локально

```bash
cd Fix-Masters
composer install
cp .env.example .env && php artisan key:generate   # если нет .env
php artisan migrate
php artisan serve
```

Откройте http://127.0.0.1:8000

## Telegram-уведомления

В `.env` укажите:

```env
TELEGRAM_BOT_TOKEN=...
TELEGRAM_CHAT_ID=...
```

Без этих переменных заявки сохраняются в БД, но сообщение в Telegram не отправляется (ошибка пишется в лог).

## Маршруты

| URL | Страница |
|-----|----------|
| `/` | Главная |
| `/quiz/device` | Квиз: тип устройства |
| `/quiz/problem` | Квиз: проблема |
| `/quiz/brand` | Квиз: марка (ноутбук / ТВ) |
| `/quiz/contact` | Контактная форма |
| `/thanks` | Страница благодарности |
| `POST /api/leads` | Приём заявок (JSON) |

Старые URL (`/quiz`, `/request`, `/pages/*.html`) перенаправляются на новые (301).

## Квиз

Логика шагов (`sessionStorage`):

- **laptop** — device → problem → brand → contact
- **pc** — device → problem → contact (brand пропускается)
- **tv** — device → problem → brand (ТВ) → contact

## Источники заявок

Фиксируются в поле `source`: хедер, баннеры, карточки услуг, опрос, футер, FAB, поп-ап, форма квиза.

## Laravel

- Документация: https://laravel.com/docs
- Пересборка Blade: `php artisan view:clear`
- Тесты: `php artisan test`
