(function () {
  const triggerButtons = Array.from(document.querySelectorAll(".header__callback-btn"));
  const contactForm = document.querySelector(".contact__form");

  if (!triggerButtons.length && !contactForm) return;

  const modalMarkup = `
    <div class="callback-modal" id="callback-modal" hidden>
      <div class="callback-modal__backdrop" data-callback-close></div>
      <div class="callback-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="callback-modal-title">
        <button type="button" class="callback-modal__close" aria-label="Закрыть" data-callback-close>×</button>
        <h2 class="callback-modal__title" id="callback-modal-title">Оставьте заявку</h2>

        <form class="callback-modal__form" id="callback-modal-form">
          <label class="callback-modal__field">
            <span class="callback-modal__label">Имя*</span>
            <input type="text" class="callback-modal__input" name="name" placeholder="Введите имя" required autocomplete="name">
          </label>

          <label class="callback-modal__field">
            <span class="callback-modal__label">Номер телефона*</span>
            <input type="tel" class="callback-modal__input" name="phone" required>
          </label>

          <label class="callback-modal__field">
            <span class="callback-modal__label">Комментарий (необязательно)</span>
            <textarea class="callback-modal__input callback-modal__input--textarea" name="comment" placeholder="Оставьте комментарий"></textarea>
          </label>

          <label class="callback-modal__consent">
            <input type="checkbox" class="callback-modal__checkbox" name="consent" required>
            <span>Я согласен(на) с <a href="#">Политикой конфиденциальности</a> и <a href="#">Условиями обработки персональных данных</a>.</span>
          </label>

          <button type="submit" class="callback-modal__submit">Оставить заявку</button>
        </form>
      </div>
    </div>

    <div class="callback-modal" id="callback-success-modal" hidden>
      <div class="callback-modal__backdrop" data-callback-success-close></div>
      <div class="callback-modal__dialog callback-modal__dialog--success" role="dialog" aria-modal="true" aria-labelledby="callback-success-title">
        <button type="button" class="callback-modal__close" aria-label="Закрыть" data-callback-success-close>×</button>
        <h2 class="callback-modal__title" id="callback-success-title">Благодарим за заявку</h2>
        <p class="callback-modal__success-text">
          <span class="callback-modal__success-line">Запрос направлен специалистам, с вами свяжутся</span><br>
          <span class="callback-modal__success-line">в ближайшее рабочее время.</span><br>
          <span class="callback-modal__success-line">Мы работаем <strong>ежедневно 09:00–21:00</strong></span>
        </p>
        <button type="button" class="callback-modal__submit callback-modal__submit--home" data-callback-home>На главную</button>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML("beforeend", modalMarkup);

  if (window.FixMastersPhoneMask) {
    window.FixMastersPhoneMask.initAll(document.body);
    window.FixMastersPhoneMask.bindForms(document.body);
  }

  const modal = document.getElementById("callback-modal");
  const successModal = document.getElementById("callback-success-modal");
  const form = document.getElementById("callback-modal-form");
  const closeButtons = Array.from(document.querySelectorAll("[data-callback-close]"));
  const successCloseButtons = Array.from(document.querySelectorAll("[data-callback-success-close]"));
  const homeButton = document.querySelector("[data-callback-home]");

  function lockBody(isLocked) {
    document.body.style.overflow = isLocked ? "hidden" : "";
  }

  function openModal() {
    if (!modal) return;
    modal.hidden = false;
    lockBody(true);
  }

  function closeModal() {
    if (!modal) return;
    modal.hidden = true;
    if (successModal && successModal.hidden) {
      lockBody(false);
    }
  }

  function openSuccessModal() {
    if (!successModal) return;
    successModal.hidden = false;
    lockBody(true);
  }

  function closeSuccessModal() {
    if (!successModal) return;
    successModal.hidden = true;
    if (modal && modal.hidden) {
      lockBody(false);
    }
  }

  function submitLead(formElement, source) {
    const formData = new FormData(formElement);
    const payload = Object.fromEntries(formData.entries());
    console.log("TG placeholder payload:", { source, ...payload });
    openSuccessModal();
    formElement.reset();
  }

  triggerButtons.forEach((button) => {
    button.addEventListener("click", openModal);
  });

  closeButtons.forEach((button) => {
    button.addEventListener("click", closeModal);
  });

  successCloseButtons.forEach((button) => {
    button.addEventListener("click", closeSuccessModal);
  });

  if (homeButton) {
    homeButton.addEventListener("click", () => {
      const indexUrl = (window.FixMastersPaths && window.FixMastersPaths.indexUrl) || "./";
      const isIndexPage = /index\.html$/.test(window.location.pathname)
        || (window.location.pathname.endsWith("/") && !window.location.pathname.includes("/pages/"));
      if (isIndexPage) {
        closeSuccessModal();
        return;
      }
      window.location.href = indexUrl;
    });
  }

  function validatePhones(formElement) {
    const phones = formElement.querySelectorAll('input[type="tel"]');
    let valid = true;
    phones.forEach((input) => {
      if (window.FixMastersPhoneMask && !window.FixMastersPhoneMask.isComplete(input)) {
        input.setCustomValidity("Введите номер телефона полностью");
        valid = false;
      }
    });
    return valid;
  }

  if (form) {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!validatePhones(form) || !form.reportValidity()) return;
      closeModal();
      submitLead(form, "header-callback");
    });
  }

  if (contactForm) {
    contactForm.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!validatePhones(contactForm) || !contactForm.reportValidity()) return;
      submitLead(contactForm, "contact-faq");
    });
  }

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") return;
    if (successModal && !successModal.hidden) {
      closeSuccessModal();
      return;
    }
    if (modal && !modal.hidden) {
      closeModal();
    }
  });
})();
