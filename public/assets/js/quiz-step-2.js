(function () {
  const paths = window.FixMastersPaths || {
    quizDeviceUrl: () => "/quiz/device",
    quizContactUrl: () => "/quiz/contact",
    quizBrandUrl: () => "/quiz/brand",
  };
  const quiz = window.FixMastersQuiz;
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

  function getProblemLabel(option) {
    return option.querySelector(".quiz-step2-option__text")?.innerText.replace(/\s+/g, " ").trim() || "";
  }

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

  function setCustomSelected(isSelected) {
    if (!customWrap) return;
    customWrap.classList.toggle("is-selected", isSelected);
  }

  function collectProblems() {
    return options
      .filter((option) => option.classList.contains("is-selected"))
      .map(getProblemLabel)
      .filter(Boolean);
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
      clearAllOptionSelection();
      setCustomSelected(true);
    });

    customInput.addEventListener("click", () => {
      clearAllOptionSelection();
      setCustomSelected(true);
    });

    customInput.addEventListener("input", () => {
      clearAllOptionSelection();
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
      window.location.href = paths.quizDeviceUrl();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      const hasOptionSelection = options.some((option) => option.classList.contains("is-selected"));
      const customText = customInput ? customInput.value.trim() : "";
      if (!hasOptionSelection && !customText) {
        openRequiredModal();
        return;
      }

      if (quiz) {
        quiz.setProblems(collectProblems(), customText);
      }

      if (window.FixMastersAnalytics) {
        window.FixMastersAnalytics.trackQuizStep("problem");
      }

      const device = quiz ? quiz.getDevice() : sessionStorage.getItem("fixMastersQuizDevice");
      if (device === "pc") {
        window.location.href = paths.quizContactUrl();
        return;
      }
      window.location.href = paths.quizBrandUrl();
    });
  }
})();
