@extends('layouts.fix-masters')

@section('title', 'FIX-MASTERS — Квиз, шаг 3')

@section('bodyClass', 'quiz-page')

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

        <div class="quiz__content quiz__content--narrow">
          <div class="quiz__meta-info" aria-hidden="true">
            <p class="hero-meta-info__line">
              Прием заявок на сайте <span class="hero-meta-info__accent">24 часа в сутки, 7 дней в неделю</span>
            </p>
            <p class="hero-meta-info__line">
              Время работы: <strong>Ежедневно 9:00 - 21:00</strong>
            </p>
          </div>

          <p class="quiz__step">Шаг 3 из 3</p>
          <h1 class="quiz__title quiz__title--step3-mobile">Укажите марку вашего ноутбука</h1>

          <div class="quiz-step2__choices" role="group" aria-label="Выберите марку устройства">
            <button type="button" class="quiz-step2-option" aria-pressed="false" data-brand="Apple">
              <span class="quiz-step2-option__text">Apple</span>
              <span class="quiz-step2-option__check" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/>
                  <path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
            </button>
            <button type="button" class="quiz-step2-option" aria-pressed="false" data-brand="Lenovo"><span class="quiz-step2-option__text">Lenovo</span><span class="quiz-step2-option__check" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/><path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span></button>
            <button type="button" class="quiz-step2-option" aria-pressed="false" data-brand="Acer"><span class="quiz-step2-option__text">Acer</span><span class="quiz-step2-option__check" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/><path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span></button>
            <button type="button" class="quiz-step2-option" aria-pressed="false" data-brand="HP"><span class="quiz-step2-option__text">HP</span><span class="quiz-step2-option__check" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/><path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span></button>
            <button type="button" class="quiz-step2-option" aria-pressed="false" data-brand="ASUS"><span class="quiz-step2-option__text">ASUS</span><span class="quiz-step2-option__check" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/><path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span></button>
            <button type="button" class="quiz-step2-option" aria-pressed="false" data-brand="Xiaomi"><span class="quiz-step2-option__text">Xiaomi</span><span class="quiz-step2-option__check" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/><path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span></button>
          </div>

          <div class="quiz-step3__extra">
            <button type="button" class="quiz-step2-option quiz-step3-option" aria-pressed="false" id="quiz-step3-other-btn">
              <span class="quiz-step2-option__text">Выбрать другой бренд</span>
            </button>
            <div class="quiz-step2__custom-wrap quiz-step3__custom-wrap" id="quiz-step3-selected">
              <input type="text" class="quiz-step2__custom-input" placeholder="Введите название нужного бренда" autocomplete="off">
            </div>
          </div>

          <div class="quiz__actions quiz__actions--double">
            <button type="button" class="quiz__btn quiz__btn--prev quiz-step2__btn--prev">
              <span aria-hidden="true">←</span>
              <span>Предыдущий шаг</span>
            </button>
            <button type="button" class="quiz__btn quiz__btn--next quiz-step2__btn--next">
              <span>Следующий шаг</span>
              <span aria-hidden="true">→</span>
            </button>
          </div>

          <div class="quiz-progress" aria-label="Прогресс квиза">
            <div class="quiz-progress__track" aria-hidden="true">
              <span class="quiz-progress__fill quiz-progress__fill--step3"></span>
              <span class="quiz-progress__label">3/3</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/phone-mask.js') }}"></script>
<script src="{{ asset('assets/js/site-fab.js') }}"></script>
<script src="{{ asset('assets/js/quiz-step-3.js') }}"></script>
<script src="{{ asset('assets/js/callback-modal.js') }}"></script>
<script src="{{ asset('assets/js/cookie-consent.js') }}"></script>
@endpush
