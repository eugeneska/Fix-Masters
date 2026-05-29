(function () {
  const triggerButtons = Array.from(document.querySelectorAll(".header__callback-btn"));
  const contactForm = document.querySelector(".contact__form");

  if (!triggerButtons.length && !contactForm) return;

  const paths = window.FixMastersPaths || {};
  const privacyUrl = paths.privacyPolicyUrl ? paths.privacyPolicyUrl() : "/privacy";

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
            <span>Я согласен(на) с <a href="${privacyUrl}">Политикой конфиденциальности</a> и <a href="${privacyUrl}">Условиями обработки персональных данных</a>.</span>
          </label>

          <button type="submit" class="callback-modal__submit">Оставить заявку</button>
        </form>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML("beforeend", modalMarkup);

  if (window.FixMastersPhoneMask) {
    window.FixMastersPhoneMask.initAll(document.body);
    window.FixMastersPhoneMask.bindForms(document.body);
  }

  const modal = document.getElementById("callback-modal");
  const form = document.getElementById("callback-modal-form");
  const closeButtons = Array.from(document.querySelectorAll("[data-callback-close]"));
  const leads = window.FixMastersLeads;
  const paths = window.FixMastersPaths || {};

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
    lockBody(false);
  }

  triggerButtons.forEach((button) => {
    button.addEventListener("click", openModal);
  });

  closeButtons.forEach((button) => {
    button.addEventListener("click", closeModal);
  });

  function validatePhones(formElement) {
    const phones = formElement.querySelectorAll('input[type="tel"]');
    let valid = true;
    phones.forEach((input) => {
      if (window.FixMastersPhoneMask && !window.FixMastersPhoneMask.isComplete(input)) {
        input.setCustomValidity("Введите номер телефона полностью");
        valid = false;
      } else {
        input.setCustomValidity("");
      }
    });
    return valid;
  }

  async function submitLead(formElement, source) {
    const formData = new FormData(formElement);
    const result = await leads.submit({
      source,
      name: String(formData.get("name") || "").trim(),
      phone: String(formData.get("phone") || "").trim(),
      comment: String(formData.get("comment") || "").trim(),
      consent: formData.get("consent") === "on",
      quizAnswers: null,
    });

    formElement.reset();
    window.location.href = result.redirect || paths.thanksUrl || "/thanks";
  }

  if (form) {
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      if (!validatePhones(form) || !form.reportValidity() || !leads) return;

      const submitBtn = form.querySelector(".callback-modal__submit");
      if (submitBtn) submitBtn.disabled = true;

      try {
        closeModal();
        await submitLead(form, leads.getSource());
      } catch (error) {
        alert(error.message || "Не удалось отправить заявку.");
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  if (contactForm) {
    contactForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      if (!validatePhones(contactForm) || !contactForm.reportValidity() || !leads) return;

      const submitBtn = contactForm.querySelector(".contact__submit");
      if (submitBtn) submitBtn.disabled = true;

      try {
        await submitLead(contactForm, leads.SOURCES.footerForm);
      } catch (error) {
        alert(error.message || "Не удалось отправить заявку.");
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && modal && !modal.hidden) {
      closeModal();
    }
  });
})();
