@extends('layouts.fix-masters')

@section('title', 'FIX-MASTERS — Квиз, шаг 1')

@section('bodyClass', 'quiz-page')

@section('content')
<main class="main main--quiz">
    <section class="quiz">
      <div class="quiz__inner">
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

        <div class="quiz__content">
          <div class="quiz__meta-info" aria-hidden="true">
            <p class="hero-meta-info__line">
              Прием заявок на сайте <span class="hero-meta-info__accent">24 часа в сутки, 7 дней в неделю</span>
            </p>
            <p class="hero-meta-info__line">
              Время работы: <strong>Ежедневно 9:00 - 21:00</strong>
            </p>
          </div>

          <p class="quiz__step">Шаг 1 из 3</p>
          <h1 class="quiz__title quiz__title--step1-mobile">
            <span class="quiz__title-line">Что именно сломалось<br class="quiz__title-break-mobile"> или&nbsp;с&nbsp;каким устройством<br class="quiz__title-break-mobile"> нужна помощь?</span>
          </h1>

          <div class="quiz__choices" role="radiogroup" aria-label="Выберите тип устройства">
            <button type="button" class="quiz-card" role="radio" aria-checked="false" data-quiz-option="laptop">
              <span class="quiz-card__check" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/>
                  <path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <img src="{{ asset('images/question1/laptop.webp') }}" alt="Ноутбук" class="quiz-card__image" width="240" height="150">
              <span class="quiz-card__label">Ноутбук</span>
            </button>

            <button type="button" class="quiz-card" role="radio" aria-checked="false" data-quiz-option="pc">
              <span class="quiz-card__check" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/>
                  <path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <img src="{{ asset('images/question1/systemnik.webp') }}" alt="Компьютер" class="quiz-card__image" width="240" height="150">
              <span class="quiz-card__label">Компьютер</span>
            </button>

            <button type="button" class="quiz-card" role="radio" aria-checked="false" data-quiz-option="tv">
              <span class="quiz-card__check" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="9" cy="9" r="7.25" stroke="#E8380D" stroke-width="1.5"/>
                  <path d="M5.75 9.1L8.1 11.35L12.25 6.85" stroke="#E8380D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <img src="{{ asset('images/question1/tw.webp') }}" alt="Телевизор" class="quiz-card__image" width="240" height="150">
              <span class="quiz-card__label">Телевизор</span>
            </button>
          </div>

          <div class="quiz__actions">
            <button type="button" class="quiz__btn quiz__btn--next quiz__next-btn">
              <span>Следующий шаг</span>
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M17.25 15.75L21 12L17.25 8.25M21 12H3" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>

          <div class="quiz-progress" aria-label="Прогресс квиза">
            <div class="quiz-progress__track" aria-hidden="true">
              <span class="quiz-progress__fill quiz-progress__fill--step1"></span>
              <span class="quiz-progress__label">1/3</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <div class="quiz-required-modal" id="quiz-required-modal" hidden>
    <div class="quiz-required-modal__backdrop" data-required-close></div>
    <div class="quiz-required-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="quiz-required-title">
      <button type="button" class="quiz-required-modal__close" aria-label="Закрыть" data-required-close>×</button>
      <h2 class="quiz-required-modal__title" id="quiz-required-title">Выберите один вариант</h2>
      <p class="quiz-required-modal__text">Чтобы перейти дальше, сначала выберите один пункт.</p>
      <button type="button" class="quiz__btn quiz__btn--next quiz-required-modal__btn" data-required-close>Понятно</button>
    </div>
  </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/phone-mask.js') }}"></script>
<script src="{{ asset('assets/js/site-fab.js') }}"></script>
<script src="{{ asset('assets/js/quiz-page.js') }}"></script>
<script src="{{ asset('assets/js/callback-modal.js') }}"></script>
<script src="{{ asset('assets/js/cookie-consent.js') }}"></script>
@endpush
