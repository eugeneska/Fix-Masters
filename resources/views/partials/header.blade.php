<header class="header">
  <div class="header__inner">
    <a href="{{ route('home') }}" class="header__logo" aria-label="FIX-MASTERS — на главную">
      <img src="{{ asset('images/logo.webp') }}" alt="FIX-MASTERS" class="header__logo-img" width="435" height="47">
    </a>

    <div class="header__info">
      <p class="header__info-line">
        Время работы: <strong>Ежедневно 9:00–21:00</strong>
      </p>
      <p class="header__info-line">
        Прием заявок на сайте <span class="header__info-accent">24 часа в сутки, 7 дней в неделю</span>
      </p>
    </div>

    <button type="button" class="header__callback-btn" data-lead-source="header_callback" data-analytics-event="button_click" data-analytics-label="Заказать обратный звонок" aria-label="Позвонить нам">
      <span class="header__callback-text">Заказать обратный звонок</span>
      <svg class="header__callback-icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M2.625 7.875C2.625 17.5397 10.4603 25.375 20.125 25.375H22.75C23.4462 25.375 24.1139 25.0984 24.6062 24.6062C25.0984 24.1139 25.375 23.4462 25.375 22.75V21.1493C25.375 20.5473 24.9655 20.0223 24.381 19.8765L19.2208 18.5862C18.7075 18.4578 18.1685 18.6503 17.8523 19.0727L16.7207 20.5812C16.3917 21.0198 15.8235 21.2135 15.309 21.0245C13.399 20.3223 11.6645 19.2134 10.2256 17.7744C8.78665 16.3355 7.67769 14.601 6.9755 12.691C6.7865 12.1765 6.98017 11.6083 7.41883 11.2793L8.92733 10.1477C9.35083 9.8315 9.54217 9.29133 9.41383 8.77917L8.1235 3.619C8.05249 3.33514 7.88867 3.08315 7.65806 2.90306C7.42745 2.72297 7.14327 2.6251 6.85067 2.625H5.25C4.55381 2.625 3.88613 2.90156 3.39384 3.39384C2.90156 3.88613 2.625 4.55381 2.625 5.25V7.875Z" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>
</header>
