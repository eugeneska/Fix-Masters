# FIX-MASTERS

Статический сайт сервиса ремонта техники (Минск).

## Структура проекта

```
Fix-Masters/
├── index.html              # Главная страница
├── fonp.png                # Фон (исходник)
├── fonp.webp               # Фон на сайте (сжатый)
├── README.md
├── assets/
│   ├── css/
│   │   └── style.css       # Общие стили
│   └── js/
│       ├── paths.js        # Пути между главной и квизом
│       ├── site-fab.js     # Плавающая кнопка опроса
│       ├── services-quiz.js
│       ├── callback-modal.js
│       ├── quiz-page.js
│       ├── quiz-step-2.js
│       ├── quiz-step-3.js
│       └── quiz-step-3-tv.js
├── pages/
│   ├── quiz.html           # Квиз, шаг 1 — тип устройства
│   ├── quiz-step-2.html    # Шаг 2 — проблема
│   ├── quiz-step-3.html    # Шаг 3 — марка ноутбука
│   ├── quiz-step-3-tv.html # Шаг 3 — марка ТВ
│   └── request.html        # Форма заявки
└── images/
    ├── hero/               # Логотипы брендов, hero
    ├── repair/             # Карточки услуг
    ├── services/           # Блок преимуществ
    ├── question1/         # Квиз, шаг 1
    ├── oplata/             # Способы оплаты
    ├── form.jpg
    └── logo.png
```

## Запуск локально

```bash
cd Fix-Masters
python3 -m http.server 8080
```

Откройте http://localhost:8080

## Квиз

Маршрут зависит от типа устройства (`sessionStorage`: `fixMastersQuizDevice`):

- **laptop** — шаги 1 → 2 → 3 (бренды) → заявка
- **pc** — шаги 1 → 2 → заявка
- **tv** — шаги 1 → 2 → 3 (бренды ТВ) → заявка
