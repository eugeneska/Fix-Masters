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

## Админка (этап 3)

URL: http://127.0.0.1:8000/admin

После миграций создайте администратора:

```bash
php artisan db:seed --class=AdminUserSeeder
```

В `.env` задайте `ADMIN_EMAIL` и `ADMIN_PASSWORD` (или используйте значения по умолчанию из сидера).

### Разделы

| URL | Описание |
|-----|----------|
| `/admin/login` | Вход |
| `/admin/leads` | Список заявок, фильтры, квалификация/качество |
| `/admin/leads/{id}` | Карточка заявки |
| `/admin/export/csv` | Выгрузка CSV с учётом фильтров |
| `/admin/settings/telegram` | Настройки Telegram-бота |

При выборе **Качество лида → Да** конверсия отправляется автоматически (один раз на систему):

| Канал трафика | GA4 | Метрика |
|---------------|-----|---------|
| Google Реклама (`gclid` или UTM Google) | да | да |
| Яндекс Реклама (`yclid` или UTM Яндекс) | нет | да |
| Соцсети, органика, прямые, рефералы | да | да |

Определение канала — по `gclid`, `yclid` и UTM-меткам, сохранённым при заявке.

### Аналитика

В `.env` укажите идентификаторы счётчиков и секреты Measurement Protocol:

```env
GTM_CONTAINER_ID=GTM-KNVT3D4H
GA4_MEASUREMENT_ID=
GA4_API_SECRET=
GA4_CONVERSION_EVENT=quality_lead

YANDEX_METRIKA_ID=
YANDEX_METRIKA_OAUTH_TOKEN=
YANDEX_METRIKA_GOAL=quality_lead
```

События с сайта (`public/assets/js/analytics.js`) уходят в `dataLayer`, GA4 (`gtag`) и Метрику (`reachGoal`).

**GA4** — события появятся в «Отчёты → Вовлечённость → События» автоматически. Отметьте как конверсии: `lead_success`, `footer_form`, `header_callback`, `quiz_contact`.

**Метрика** — в «Настройки → Цели» создайте цели типа «JavaScript-событие», идентификатор **совпадает** с именем события:

| Идентификатор | Когда |
|---------------|--------|
| `button_click` | Клик по основным кнопкам |
| `quiz_start` | Открытие `/quiz/device` |
| `quiz_step` | Шаг квиза (device / brand / problem) |
| `footer_form` | Успешная заявка из футера |
| `header_callback` | Успешная заявка «Обратный звонок» |
| `quiz_contact` | Успешная заявка из квиза |
| `lead_success` | Страница `/thanks` |
| `popup_event` | Открытие / закрытие поп-апа |
| `fab_event` | Плавающая кнопка |

Конверсии из админки (`GA4_CONVERSION_EVENT`, `YANDEX_METRIKA_GOAL` в `.env`) настраиваются отдельно — это офлайн-отправка по API, не JavaScript-событие на сайте.

OAuth-токен для офлайн-конверсий: приложение в [Яндекс OAuth](https://oauth.yandex.com/) с доступом `metrika:write` или `metrika:offline_data`.

Если используете **GTM** (`GTM_CONTAINER_ID`), GA4 на сайте не дублируется — настройте теги GA4 Event на события `dataLayer` в контейнере GTM.

## Laravel

- Документация: https://laravel.com/docs
- Пересборка Blade: `php artisan view:clear`
- Тесты: `php artisan test`
