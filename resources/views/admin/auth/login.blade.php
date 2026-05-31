@extends('layouts.admin')

@section('title', 'Вход')

@section('content')
  <div class="admin-login">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="admin-login__card">
      @csrf
      <h1 class="admin-login__title">Вход в админку</h1>

      @if ($errors->any())
        <div class="admin-alert admin-alert--error">
          {{ $errors->first() }}
        </div>
      @endif

      <label class="admin-field">
        <span class="admin-field__label">Email</span>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="admin-input">
      </label>

      <label class="admin-field">
        <span class="admin-field__label">Пароль</span>
        <input type="password" name="password" required class="admin-input">
      </label>

      <label class="admin-checkbox">
        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
        <span>Запомнить меня</span>
      </label>

      <button type="submit" class="admin-btn admin-btn--primary admin-btn--block">Войти</button>
    </form>
  </div>
@endsection
