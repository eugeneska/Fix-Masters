@extends('layouts.fix-masters')

@section('title', 'Политика конфиденциальности — FIX-MASTERS')

@section('content')
<main class="main">
  <section class="legal">
    <div class="legal__inner">
      <h1 class="legal__title">Политика конфиденциальности</h1>
      <p class="legal__lead">Текст политики конфиденциальности будет опубликован позже.</p>
      <div class="legal__content">
        <p>Настоящая страница является заглушкой. Здесь будет размещена информация об обработке персональных данных пользователей сайта FIX-MASTERS.</p>
        <p>Если у вас есть вопросы, свяжитесь с нами через форму на <a href="{{ route('home') }}">главной странице</a>.</p>
      </div>
      <a href="{{ route('home') }}" class="legal__back">← На главную</a>
    </div>
  </section>
</main>
@endsection
