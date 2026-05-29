@extends('layouts.fix-masters')

@section('title', 'FIX-MASTERS — Оставить заявку')

@section('bodyClass', 'quiz-page quiz-page--request')

@section('content')
<main class="main main--quiz">
    <section class="quiz">
      <div class="quiz__inner quiz__inner--step2">
        <div class="quiz__brands" aria-hidden="true">
          <img src="{{ asset('images/hero/lenovo.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--lenovo" width="150" height="60">
          <img src="{{ asset('images/hero/asus.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--asus" width="148" height="52">
          <img src="{{ asset('images/hero/hp.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--hp" width="68" height="68">
          <img src="{{ asset('images/hero/gig.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--gig" width="165" height="50">
          <img src="{{ asset('images/hero/msi.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--msi" width="100" height="40">
          <img src="{{ asset('images/hero/lg.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--lg" width="95" height="50">
          <img src="{{ asset('images/hero/apple.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--apple" width="65" height="80">
          <img src="{{ asset('images/hero/sams.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--sams" width="115" height="36">
          <img src="{{ asset('images/hero/nvid.webp') }}" alt="" class="quiz__brand-logo quiz__brand-logo--nvid" width="140" height="48">
        </div>

        <div class="quiz__content quiz__content--narrow quiz-step4">
          <div class="quiz__meta-info" aria-hidden="true">
            <p class="hero-meta-info__line">
              Прием заявок на сайте <span class="hero-meta-info__accent">24 часа в сутки, 7 дней в неделю</span>
            </p>
            <p class="hero-meta-info__line">
              Время работы: <strong>Ежедневно 9:00 - 21:00</strong>
            </p>
          </div>

          <h1 class="quiz-step4__title">Заполните форму<br>и мы с вами свяжемся</h1>

          <form class="quiz-step4__form" action="{{ route('leads.store') }}" method="post" novalidate>
            @csrf
            <label class="quiz-step4__field">
              <span class="quiz-step4__label">Имя*</span>
              <input type="text" class="quiz-step4__input" name="name" placeholder="Введите имя" required autocomplete="name">
            </label>

            <label class="quiz-step4__field">
              <span class="quiz-step4__label">Номер телефона*</span>
              <input type="tel" class="quiz-step4__input" name="phone" required>
            </label>

            <label class="quiz-step4__field">
              <span class="quiz-step4__label">Комментарий (необязательно)</span>
              <textarea class="quiz-step4__input quiz-step4__input--textarea" name="comment" placeholder="Оставьте комментарий"></textarea>
            </label>

            <label class="quiz-step4__consent">
              <input type="checkbox" class="quiz-step4__checkbox" name="consent" required>
              <span>Я согласен(на) с <a href="{{ route('privacy') }}">Политикой конфиденциальности</a> и <a href="{{ route('privacy') }}">Условиями обработки персональных данных</a>.</span>
            </label>

            <button type="submit" class="quiz__btn quiz__btn--next quiz-step4__submit">Оставить заявку</button>
          </form>
        </div>
      </div>
    </section>
  </main>
@endsection

@push('scripts')
<script>
  if (window.FixMastersLeads && !sessionStorage.getItem(window.FixMastersLeads.SOURCE_KEY)) {
    window.FixMastersLeads.setSource(window.FixMastersLeads.SOURCES.quizContact);
  }
</script>
<script src="{{ asset('assets/js/phone-mask.js') }}"></script>
<script src="{{ asset('assets/js/site-fab.js') }}"></script>
<script src="{{ asset('assets/js/quiz-contact.js') }}"></script>
<script src="{{ asset('assets/js/callback-modal.js') }}"></script>
<script src="{{ asset('assets/js/cookie-consent.js') }}"></script>
@endpush
