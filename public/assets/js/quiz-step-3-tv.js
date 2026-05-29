(function () {
  const paths = window.FixMastersPaths || {
    requestUrl: () => "/request",
    quizStep3Url: () => "/quiz/step-3",
    quizStep2Url: () => "/quiz/step-2",
  };
  const device = sessionStorage.getItem("fixMastersQuizDevice");
  if (device === "pc") {
    window.location.replace(paths.requestUrl());
    return;
  }
  if (device && device !== "tv") {
    window.location.replace(paths.quizStep3Url());
    return;
  }

  const options = Array.from(document.querySelectorAll(".quiz-step3-tv__choices .quiz-step2-option"));
  const customWrap = document.querySelector(".quiz-step3-tv__custom-wrap");
  const input = customWrap ? customWrap.querySelector(".quiz-step2__custom-input") : null;
  const prevBtn = document.querySelector(".quiz__btn--prev");
  const nextBtn = document.querySelector(".quiz__btn--next");
  const requiredModal = document.getElementById("quiz-step3-required-modal");
  const requiredCloseButtons = Array.from(document.querySelectorAll("[data-step3-required-close]"));
  if (!options.length && !prevBtn && !nextBtn) return;

  function syncOptionState() {
    options.forEach((option) => {
      const isSelected = option.classList.contains("is-selected");
      option.setAttribute("aria-pressed", String(isSelected));
    });
  }

  function toggleOption(option) {
    option.classList.toggle("is-selected");
    syncOptionState();
  }

  function hasBrandSelection() {
    const hasGridSelection = options.some((option) => option.classList.contains("is-selected"));
    const hasCustomBrand = Boolean(input && input.value.trim());
    return hasGridSelection || hasCustomBrand;
  }

  function openRequiredModal() {
    if (!requiredModal) return;
    requiredModal.hidden = false;
    document.body.style.overflow = "hidden";
  }

  function closeRequiredModal() {
    if (!requiredModal) return;
    requiredModal.hidden = true;
    document.body.style.overflow = "";
  }

  requiredCloseButtons.forEach((button) => {
    button.addEventListener("click", closeRequiredModal);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && requiredModal && !requiredModal.hidden) {
      closeRequiredModal();
    }
  });

  function setCustomSelected() {
    if (!customWrap || !input) return;
    customWrap.classList.toggle("is-selected", Boolean(input.value.trim()));
  }

  options.forEach((option) => {
    option.addEventListener("click", () => {
      toggleOption(option);
    });
  });

  if (input) {
    input.addEventListener("input", setCustomSelected);
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      window.location.href = paths.quizStep2Url();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      if (!hasBrandSelection()) {
        openRequiredModal();
        return;
      }
      window.location.href = paths.requestUrl();
    });
  }

  syncOptionState();
  setCustomSelected();
})();
