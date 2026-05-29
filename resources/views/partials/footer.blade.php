<footer class="footer">
  <div class="footer__inner">
    <div class="footer__top">
      <a href="{{ route('home') }}" class="footer__logo" aria-label="FIX-MASTERS — на главную">
        <img src="{{ asset('images/logo.webp') }}" alt="FIX-MASTERS" class="footer__logo-img" width="435" height="47">
      </a>

      <div class="footer__legal">
        <p class="footer__legal-line">ИП Алексеев Матвей Олегович</p>
        <p class="footer__legal-line">УНП: 692215394</p>
      </div>
    </div>

    <a href="{{ route('privacy') }}" class="footer__policy footer__policy--mobile">Политика конфиденциальности</a>

    <div class="footer__notice">
      <span class="footer__notice-icon" aria-hidden="true">!</span>
      <p class="footer__notice-text">
        <span class="footer__notice-line">Данный сайт носит исключительно информационный характер. Сайт не является публичной офертой. Все цены носят ознакомительный характер и могут отличаться</span>
        <span class="footer__notice-line">от итоговой стоимости ремонта, которая определяется после проведения бесплатной диагностики.</span>
      </p>
    </div>

    <div class="footer__bottom">
      <p class="footer__copy"><span class="footer__copy-mark" aria-hidden="true">©</span> 2026 FIX-MASTERS. Ремонт техники</p>
      <a href="{{ route('privacy') }}" class="footer__policy">Политика конфиденциальности</a>
    </div>
  </div>
</footer>
