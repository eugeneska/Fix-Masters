@extends('layouts.fix-masters')

@section('title', 'FIX-MASTERS — Спасибо за заявку')

@section('bodyClass', 'quiz-page quiz-page--thanks')

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

      <div class="quiz__content quiz__content--narrow quiz-step4 quiz-step4--thanks">
        <h1 class="quiz-step4__title">Благодарим за заявку</h1>
        <p class="quiz-step4__thanks-text">
          <span class="quiz-step4__thanks-line">Запрос направлен специалистам, с вами свяжутся</span>
          <span class="quiz-step4__thanks-line">в ближайшее рабочее время.</span>
          <span class="quiz-step4__thanks-line">Мы работаем <strong>ежедневно 09:00–21:00</strong></span>
        </p>
        <div class="quiz-step4__actions quiz-step4__actions--thanks">
          <button type="button" class="quiz__btn quiz__btn--next quiz-step4__home-btn" id="thanks-home-btn" data-analytics-event="button_click" data-analytics-label="На главную">На главную</button>
        </div>
      </div>
    </div>
  </section>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/thanks-page.js') }}"></script>
<script src="{{ asset('assets/js/phone-mask.js') }}"></script>
<script src="{{ asset('assets/js/site-fab.js') }}"></script>
<script src="{{ asset('assets/js/callback-modal.js') }}"></script>
<script src="{{ asset('assets/js/cookie-consent.js') }}"></script>
@endpush
