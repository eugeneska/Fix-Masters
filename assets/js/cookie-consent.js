(function () {
  const STORAGE_KEY = "fixMastersCookieConsent";

  if (localStorage.getItem(STORAGE_KEY)) return;

  const markup = `
    <div class="cookie-consent" id="cookie-consent" role="dialog" aria-live="polite" aria-label="Уведомление об использовании cookie">
      <div class="cookie-consent__icon" aria-hidden="true">
        <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#cookie-consent-icon-clip)">
            <path d="M25.8105 30.6249C26.9496 30.6249 27.873 29.7015 27.873 28.5624C27.873 27.4234 26.9496 26.4999 25.8105 26.4999C24.6715 26.4999 23.748 27.4234 23.748 28.5624C23.748 29.7015 24.6715 30.6249 25.8105 30.6249Z" fill="#E8380D"/>
            <path d="M14.8125 29.25C15.9516 29.25 16.875 28.3266 16.875 27.1875C16.875 26.0484 15.9516 25.125 14.8125 25.125C13.6734 25.125 12.75 26.0484 12.75 27.1875C12.75 28.3266 13.6734 29.25 14.8125 29.25Z" fill="#E8380D"/>
            <path d="M13.4355 19.6251C14.5746 19.6251 15.498 18.7016 15.498 17.5626C15.498 16.4235 14.5746 15.5001 13.4355 15.5001C12.2965 15.5001 11.373 16.4235 11.373 17.5626C11.373 18.7016 12.2965 19.6251 13.4355 19.6251Z" fill="#E8380D"/>
            <path d="M21.6855 22.3749C22.8246 22.3749 23.748 21.4515 23.748 20.3124C23.748 19.1734 22.8246 18.2499 21.6855 18.2499C20.5465 18.2499 19.623 19.1734 19.623 20.3124C19.623 21.4515 20.5465 22.3749 21.6855 22.3749Z" fill="#E8380D"/>
            <path d="M37.5 21C35.312 21 33.2136 20.1308 31.6664 18.5836C30.1192 17.0365 29.25 14.938 29.25 12.75C27.062 12.75 24.9636 11.8808 23.4164 10.3336C21.8692 8.78646 21 6.68804 21 4.5C17.7366 4.5 14.5465 5.46771 11.8331 7.28075C9.11969 9.0938 7.00484 11.6707 5.756 14.6857C4.50715 17.7007 4.18039 21.0183 4.81705 24.219C5.45371 27.4197 7.02518 30.3597 9.33275 32.6673C11.6403 34.9748 14.5803 36.5463 17.781 37.183C20.9817 37.8196 24.2993 37.4929 27.3143 36.244C30.3293 34.9952 32.9062 32.8803 34.7193 30.1669C36.5323 27.4535 37.5 24.2634 37.5 21Z" stroke="#E8380D" stroke-width="2.475" stroke-linecap="round" stroke-linejoin="round"/>
          </g>
          <defs>
            <clipPath id="cookie-consent-icon-clip">
              <rect width="36" height="36" fill="white" transform="translate(3 3)"/>
            </clipPath>
          </defs>
        </svg>
      </div>
      <p class="cookie-consent__text cookie-consent__text--desktop">
        <span class="cookie-consent__text-line">Используя данный сайт, вы соглашаетесь с нашей</span>
        <span class="cookie-consent__text-line"><a href="#" class="cookie-consent__link">Политикой использования файлов cookie.</a></span>
      </p>
      <p class="cookie-consent__text cookie-consent__text--mobile">
        <span class="cookie-consent__text-line">Используя данный сайт, вы</span>
        <span class="cookie-consent__text-line">соглашаетесь с нашей <a href="#" class="cookie-consent__link">Политикой</a></span>
        <span class="cookie-consent__text-line"><a href="#" class="cookie-consent__link">использования файлов cookie.</a></span>
      </p>
      <div class="cookie-consent__actions">
        <button type="button" class="cookie-consent__btn cookie-consent__btn--decline" data-cookie-decline>Отказаться</button>
        <button type="button" class="cookie-consent__btn cookie-consent__btn--accept" data-cookie-accept>Окей</button>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML("beforeend", markup);
  document.body.classList.add("has-cookie-consent");

  const banner = document.getElementById("cookie-consent");
  if (!banner) return;

  const acceptBtn = banner.querySelector("[data-cookie-accept]");
  const declineBtn = banner.querySelector("[data-cookie-decline]");

  function dismiss(value) {
    localStorage.setItem(STORAGE_KEY, value);
    banner.classList.add("is-hidden");
    document.body.classList.remove("has-cookie-consent");
    window.setTimeout(function () {
      banner.remove();
    }, 320);
  }

  acceptBtn.addEventListener("click", function () {
    dismiss("accepted");
  });

  declineBtn.addEventListener("click", function () {
    dismiss("declined");
  });
})();
