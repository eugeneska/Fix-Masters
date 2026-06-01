@extends('layouts.admin')

@section('title', 'Список заявок')

@section('content')
  <div class="admin-page">
    <div class="admin-page__head">
      <h1 class="admin-page__title">Список заявок</h1>
    </div>

    <form method="GET" action="{{ route('admin.leads.index') }}" class="admin-filters">
      <div class="admin-filters__row">
        <label class="admin-field admin-field--inline">
          <span class="admin-field__label">Период</span>
          <select name="period" class="admin-select">
            <option value="all" @selected(($filters['period'] ?? 'all') === 'all')>Все</option>
            <option value="day" @selected(($filters['period'] ?? '') === 'day')>Сегодня</option>
            <option value="date" @selected(($filters['period'] ?? '') === 'date')>Конкретная дата</option>
            <option value="week" @selected(($filters['period'] ?? '') === 'week')>Неделя</option>
            <option value="month" @selected(($filters['period'] ?? '') === 'month')>Месяц</option>
            <option value="custom" @selected(($filters['period'] ?? '') === 'custom')>Произвольный</option>
          </select>
        </label>
        <label class="admin-field admin-field--inline">
          <span class="admin-field__label">С</span>
          <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="admin-input">
        </label>
        <label class="admin-field admin-field--inline">
          <span class="admin-field__label">По</span>
          <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="admin-input">
        </label>
        <label class="admin-field admin-field--inline">
          <span class="admin-field__label">Источник</span>
          <select name="source" class="admin-select">
            <option value="">Все</option>
            @foreach ($sources as $source)
              <option value="{{ $source->value }}" @selected(($filters['source'] ?? '') === $source->value)>{{ $source->label() }}</option>
            @endforeach
          </select>
        </label>
        <label class="admin-field admin-field--inline">
          <span class="admin-field__label">Квалификация</span>
          <select name="qualification" class="admin-select">
            <option value="">Все</option>
            <option value="yes" @selected(($filters['qualification'] ?? '') === 'yes')>Да</option>
            <option value="no" @selected(($filters['qualification'] ?? '') === 'no')>Нет</option>
            <option value="empty" @selected(($filters['qualification'] ?? '') === 'empty')>—</option>
          </select>
        </label>
        <label class="admin-field admin-field--inline">
          <span class="admin-field__label">Качество</span>
          <select name="quality" class="admin-select">
            <option value="">Все</option>
            <option value="yes" @selected(($filters['quality'] ?? '') === 'yes')>Да</option>
            <option value="no" @selected(($filters['quality'] ?? '') === 'no')>Нет</option>
            <option value="empty" @selected(($filters['quality'] ?? '') === 'empty')>—</option>
          </select>
        </label>
        <label class="admin-field admin-field--inline">
          <span class="admin-field__label">На странице</span>
          <select name="per_page" class="admin-select">
            @foreach ([10, 25, 50, 100] as $n)
              <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
            @endforeach
          </select>
        </label>
        <button type="submit" class="admin-btn admin-btn--primary">Применить</button>
        <a href="{{ route('admin.leads.index') }}" class="admin-btn admin-btn--ghost">Сбросить</a>
        <a href="{{ route('admin.export.csv', request()->query()) }}" class="admin-btn admin-btn--outline">CSV</a>
      </div>
    </form>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th class="admin-table__check-col">
              <input type="checkbox" class="admin-row-check" data-select-all aria-label="Выделить все">
            </th>
            <th>Дата</th>
            <th>
              <a href="{{ route('admin.leads.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => ($filters['direction'] ?? 'desc') === 'desc' ? 'asc' : 'desc'])) }}">
                Дата/время @if(($filters['sort'] ?? 'created_at') === 'created_at')↓@endif
              </a>
            </th>
            <th>Имя</th>
            <th>Телефон</th>
            <th>UTM</th>
            <th>Комментарий клиента</th>
            <th>Качественный лид</th>
            <th>Квалифицированный лид</th>
            <th>Метрика / GA4</th>
            <th>Примечание</th>
            <th>Источник</th>
            <th class="admin-table__actions-col">Действия</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($leads as $lead)
            @php
              $createdAt = $lead->created_at->timezone(config('app.timezone'));
              $clientComment = $lead->comment ?: $lead->problemsText();
            @endphp
            <tr class="admin-table__row" data-href="{{ route('admin.leads.show', $lead) }}">
              <td class="admin-table__check-col">
                <input type="checkbox" class="admin-row-check" data-lead-select value="{{ $lead->id }}" aria-label="Выделить заявку #{{ $lead->id }}">
              </td>
              <td>{{ $createdAt->format('Y-m-d') }}</td>
              <td>{{ $createdAt->format('Y-m-d H:i:s') }}</td>
              <td>{{ $lead->name }}</td>
              <td>{{ $lead->phone }}</td>
              <td class="admin-table__utm">{{ $lead->utmSummary() ?? '—' }}</td>
              <td class="admin-table__comment">{{ $clientComment ? Str::limit($clientComment, 80) : '—' }}</td>
              <td>
                @include('admin.partials.status-toggle', ['lead' => $lead, 'field' => 'quality_status'])
              </td>
              <td>
                @include('admin.partials.status-toggle', ['lead' => $lead, 'field' => 'qualification_status'])
              </td>
              <td>
                @include('admin.partials.analytics-dots', ['lead' => $lead])
              </td>
              <td>
                <form class="admin-note-form" data-lead-id="{{ $lead->id }}">
                  <input type="text" name="admin_note" value="{{ $lead->admin_note }}" class="admin-note-input" placeholder="Примечание">
                  <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm">OK</button>
                </form>
              </td>
              <td>
                <span class="admin-source-badge">{{ $lead->sourceLabel() }}</span>
              </td>
              <td class="admin-table__actions-col">
                <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" class="admin-delete-form" data-lead-id="{{ $lead->id }}">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Удалить</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="13" class="admin-table__empty">Заявок пока нет</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="admin-pagination">
      {{ $leads->links('vendor.pagination.admin') }}
    </div>
  </div>
@endsection
