@extends('layouts.admin')

@section('title', 'Заявка #'.$lead->id)

@section('content')
  <div class="admin-page admin-card-page">
    <div class="admin-card-page__head">
      <h1 class="admin-page__title">Карточка заявки #{{ $lead->id }}</h1>
      <a href="{{ route('admin.leads.index') }}" class="admin-btn admin-btn--outline">Закрыть</a>
    </div>

    <div class="admin-card-grid">
      <div class="admin-card-panel">
        <h2 class="admin-card-panel__title">Основные</h2>
        <dl class="admin-dl">
          <dt>Дата/время конверсии</dt>
          <dd>{{ $lead->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</dd>
          <dt>ID конверсии</dt>
          <dd>{{ $lead->conversion_id }}</dd>
          <dt>Источник кнопки</dt>
          <dd>{{ $lead->sourceLabel() }}</dd>
          <dt>Имя</dt>
          <dd>{{ $lead->name }}</dd>
          <dt>Телефон</dt>
          <dd>{{ $lead->phone }}</dd>
          <dt>Комментарий</dt>
          <dd>{{ $lead->comment ?: '—' }}</dd>
          @if ($lead->messenger)
            <dt>Мессенджер</dt>
            <dd>{{ $lead->messenger }}</dd>
          @endif
        </dl>

        <h2 class="admin-card-panel__title">Ответы квиза</h2>
        <pre class="admin-quiz-path">{{ $lead->quizPathText() }}</pre>

        <h2 class="admin-card-panel__title">Трекинг и путь</h2>
        <dl class="admin-dl">
          <dt>URL формы</dt>
          <dd>@if($lead->form_url)<a href="{{ $lead->form_url }}" target="_blank" rel="noopener">{{ $lead->form_url }}</a>@else — @endif</dd>
          <dt>Первый контакт</dt>
          <dd>@if($lead->first_contact_url)<a href="{{ $lead->first_contact_url }}" target="_blank" rel="noopener">{{ $lead->first_contact_url }}</a>@else — @endif</dd>
          <dt>Последнее нажатие</dt>
          <dd>{{ $lead->last_click ?: '—' }}</dd>
          <dt>Реферер</dt>
          <dd>{{ $lead->referrer ?: '—' }}</dd>
          <dt>UTM source</dt>
          <dd>{{ $lead->utm_source ?: '—' }}</dd>
          <dt>UTM medium</dt>
          <dd>{{ $lead->utm_medium ?: '—' }}</dd>
          <dt>UTM campaign</dt>
          <dd>{{ $lead->utm_campaign ?: '—' }}</dd>
          <dt>UTM content</dt>
          <dd>{{ $lead->utm_content ?: '—' }}</dd>
          <dt>UTM term</dt>
          <dd>{{ $lead->utm_term ?: '—' }}</dd>
          <dt>GCLID</dt>
          <dd>{{ $lead->gclid ?: '—' }}</dd>
          <dt>YCLID</dt>
          <dd>{{ $lead->yclid ?: '—' }}</dd>
          <dt>YM Client ID</dt>
          <dd>{{ $lead->ym_client_id ?: '—' }}</dd>
          <dt>GA Client ID</dt>
          <dd>{{ $lead->ga_client_id ?: '—' }}</dd>
          <dt>IP-адрес</dt>
          <dd>{{ $lead->ip ?: '—' }}</dd>
          <dt>Telegram</dt>
          <dd>{{ $lead->telegram_sent ? 'отправлено' : 'не отправлено' }}</dd>
        </dl>
      </div>

      <div class="admin-card-panel admin-card-panel--actions" data-lead-id="{{ $lead->id }}">
        <h2 class="admin-card-panel__title">Квалификация и качество</h2>

        <div class="admin-card-field">
          <span class="admin-field__label">Квалификация лида</span>
          @include('admin.partials.status-toggle', ['lead' => $lead, 'field' => 'qualification_status'])
        </div>

        <div class="admin-card-field">
          <span class="admin-field__label">Качество лида</span>
          @include('admin.partials.status-toggle', ['lead' => $lead, 'field' => 'quality_status'])
        </div>

        <div class="admin-card-field">
          <span class="admin-field__label">Канал трафика</span>
          <p class="admin-conversion-summary">{{ $lead->trafficChannelLabel() }}</p>
        </div>

        <div class="admin-card-field">
          <span class="admin-field__label">Конверсия в аналитику</span>
          <p class="admin-conversion-summary">{{ $lead->conversionStatusSummary() }}</p>
          <p class="admin-conversion-summary">При отметке «Качество → Да» событие «Качественный лид» отправится автоматически:
            @if ($lead->conversionTarget() === 'metrika')
              только в Яндекс Метрику (канал: {{ $lead->trafficChannelLabel() }}).
            @else
              в GA4 и Яндекс Метрику (канал: {{ $lead->trafficChannelLabel() }}).
            @endif
          </p>
          @include('admin.partials.analytics-dots', ['lead' => $lead])
        </div>

        <label class="admin-field">
          <span class="admin-field__label">Примечание</span>
          <textarea name="admin_note" class="admin-textarea" rows="5" data-note-input>{{ $lead->admin_note }}</textarea>
        </label>

        <button type="button" class="admin-btn admin-btn--primary admin-btn--block" data-save-note>Сохранить комментарий</button>
      </div>
    </div>
  </div>
@endsection
