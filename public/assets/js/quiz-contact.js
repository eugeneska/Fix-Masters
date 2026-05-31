(function () {
  const form = document.querySelector(".quiz-step4__form");
  if (!form) return;

  const paths = window.FixMastersPaths || {};
  const submitBtn = form.querySelector(".quiz-step4__submit");

  if (window.FixMastersPhoneMask) {
    window.FixMastersPhoneMask.initAll(form);
    window.FixMastersPhoneMask.bindForms(form);
  }

  function validatePhones() {
    const phones = form.querySelectorAll('input[type="tel"]');
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

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!validatePhones() || !form.reportValidity()) return;

    const nameInput = form.querySelector('input[type="text"]');
    const phoneInput = form.querySelector('input[type="tel"]');
    const commentInput = form.querySelector("textarea");
    const consentInput = form.querySelector('input[type="checkbox"]');

    const leads = window.FixMastersLeads;
    const quiz = window.FixMastersQuiz;

    if (!leads) return;

    if (submitBtn) {
      submitBtn.disabled = true;
    }

    try {
      const result = await leads.submit({
        source: leads.getSource(),
        name: nameInput ? nameInput.value.trim() : "",
        phone: phoneInput ? phoneInput.value.trim() : "",
        comment: commentInput ? commentInput.value.trim() : "",
        consent: consentInput ? consentInput.checked : false,
        quizAnswers: quiz ? quiz.getAnswersForSubmit() : null,
        formElement: form,
      });

      if (quiz) {
        quiz.reset();
      }
      sessionStorage.removeItem(leads.SOURCE_KEY);

      window.location.href = result.redirect || (paths.thanksUrl ? paths.thanksUrl() : "/thanks");
    } catch (error) {
      alert(error.message || "Не удалось отправить заявку. Попробуйте позже.");
      if (submitBtn) {
        submitBtn.disabled = false;
      }
    }
  });
})();
