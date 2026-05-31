@extends('layouts.admin')

@section('title', 'Настройки Telegram')

@section('content')
  <div class="admin-page admin-settings">
    <h1 class="admin-page__title">Настройки Telegram-бота</h1>
    <p class="admin-page__hint">Значения из админки имеют приоритет над переменными в <code>.env</code>. Оставьте поле пустым, чтобы использовать значение из <code>.env</code>.</p>

    <form method="POST" action="{{ route('admin.settings.telegram.update') }}" class="admin-settings__form">
      @csrf
      @method('PUT')

      <label class="admin-field">
        <span class="admin-field__label">Bot Token</span>
        <input type="text" name="telegram_bot_token" value="{{ old('telegram_bot_token', $botToken) }}" class="admin-input" autocomplete="off">
      </label>

      <label class="admin-field">
        <span class="admin-field__label">Chat ID</span>
        <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id', $chatId) }}" class="admin-input" autocomplete="off">
      </label>

      <button type="submit" class="admin-btn admin-btn--primary">Сохранить</button>
    </form>
  </div>
@endsection
