(function () {
  const paths = window.FixMastersPaths || {
    quizUrl: () => "/pages/quiz.html",
    requestUrl: () => "/pages/request.html",
    quizStep3Url: () => "/pages/quiz-step-3.html",
    quizStep3TvUrl: () => "/pages/quiz-step-3-tv.html",
  };
  const options = Array.from(document.querySelectorAll(".quiz-step2-option"));
  const customWrap = document.getElementById("quiz-step2-custom");
  const customInput = customWrap ? customWrap.querySelector(".quiz-step2__custom-input") : null;
  const prevBtn = document.querySelector(".quiz-step2__btn--prev");
  const nextBtn = document.querySelector(".quiz-step2__btn--next");
  const requiredModal = document.getElementById("quiz-step2-required-modal");
  const requiredCloseButtons = Array.from(document.querySelectorAll("[data-step2-required-close]"));
  if (!options.length && !customInput && !prevBtn && !nextBtn) return;

  const unknownOption = options.find((option) => option.dataset.problemOption === "unknown") || null;
  const regularOptions = options.filter((option) => option.dataset.problemOption === "true");

  function setOptionSelected(option, isSelected) {
    option.classList.toggle("is-selected", isSelected);
    option.setAttribute("aria-checked", String(isSelected));
  }

  function clearRegularOptions() {
    regularOptions.forEach((option) => {
      setOptionSelected(option, false);
    });
  }

  function clearUnknownOption() {
    if (!unknownOption) return;
    setOptionSelected(unknownOption, false);
  }

  function toggleRegularOption(nextOption) {
    const isSelected = nextOption.classList.contains("is-selected");
    setOptionSelected(nextOption, !isSelected);
    clearUnknownOption();
  }

  function selectUnknownOption() {
    if (!unknownOption) return;
    clearRegularOptions();
    setOptionSelected(unknownOption, true);
  }

  function clearAllOptionSelection() {
    options.forEach((option) => {
      setOptionSelected(option, false);
    });
  }

  function clearOptionSelection() {
    clearAllOptionSelection();
  }

  function setCustomSelected(isSelected) {
    if (!customWrap) return;
    customWrap.classList.toggle("is-selected", isSelected);
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

  options.forEach((option) => {
    option.addEventListener("click", () => {
      if (option === unknownOption) {
        selectUnknownOption();
      } else {
        toggleRegularOption(option);
      }
      setCustomSelected(false);
    });
  });

  if (customInput) {
    customInput.addEventListener("focus", () => {
      clearOptionSelection();
      setCustomSelected(true);
    });

    customInput.addEventListener("click", () => {
      clearOptionSelection();
      setCustomSelected(true);
    });

    customInput.addEventListener("input", () => {
      clearOptionSelection();
      setCustomSelected(true);
    });

    customInput.addEventListener("blur", () => {
      if (!customInput.value.trim()) {
        setCustomSelected(false);
      }
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      window.location.href = paths.quizUrl();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      const hasOptionSelection = options.some((option) => option.classList.contains("is-selected"));
      const hasCustomText = Boolean(customInput && customInput.value.trim());
      if (!hasOptionSelection && !hasCustomText) {
        openRequiredModal();
        return;
      }
      const device = sessionStorage.getItem("fixMastersQuizDevice");
      if (device === "pc") {
        window.location.href = paths.requestUrl();
        return;
      }
      const nextUrl = device === "tv" ? paths.quizStep3TvUrl() : paths.quizStep3Url();
      window.location.href = nextUrl;
    });
  }
})();
