(function () {
  const paths = window.FixMastersPaths || {
    quizContactUrl: () => "/quiz/contact",
    quizProblemUrl: () => "/quiz/problem",
  };
  const quiz = window.FixMastersQuiz;
  const device = quiz ? quiz.getDevice() : sessionStorage.getItem("fixMastersQuizDevice");

  if (device === "pc") {
    window.location.replace(paths.quizContactUrl());
    return;
  }

  const laptopPanel = document.getElementById("quiz-brand-laptop");
  const tvPanel = document.getElementById("quiz-brand-tv");
  const isTv = device === "tv";

  if (isTv && tvPanel) {
    tvPanel.hidden = false;
  } else if (laptopPanel) {
    laptopPanel.hidden = false;
  } else {
    window.location.replace(paths.quizProblemUrl());
    return;
  }

  const activePanel = isTv ? tvPanel : laptopPanel;
  const options = Array.from(activePanel.querySelectorAll(".quiz-step2-option[data-brand]"));
  const customWrap = activePanel.querySelector(".quiz-step2__custom-wrap, .quiz-step3__custom-wrap, .quiz-step3-tv__custom-wrap");
  const customInput = customWrap ? customWrap.querySelector(".quiz-step2__custom-input") : null;
  const otherBtn = document.getElementById("quiz-step3-other-btn");
  const prevBtn = document.querySelector(".quiz-step2__btn--prev");
  const nextBtn = document.querySelector(".quiz-step2__btn--next");
  const requiredModal = document.getElementById("quiz-step3-required-modal");
  const requiredCloseButtons = Array.from(document.querySelectorAll("[data-step3-required-close]"));
  const modal = document.getElementById("brand-modal");
  const modalItems = Array.from(document.querySelectorAll(".quiz-modal__item"));
  const modalCloseButtons = Array.from(document.querySelectorAll("[data-modal-close], [data-modal-cancel]"));
  const modalAcceptBtn = document.querySelector("[data-modal-accept]");

  let selectedPopupBrands = [];
  let draftPopupBrands = [];

  function collectBrandValue() {
    const selected = options
      .filter((option) => option.classList.contains("is-selected"))
      .map((option) => option.dataset.brand || option.querySelector(".quiz-step2-option__text")?.textContent?.trim())
      .filter(Boolean);

    if (customInput && customInput.value.trim()) {
      selected.push(customInput.value.trim());
    }

    return selected.join(", ");
  }

  function hasBrandSelection() {
    return Boolean(collectBrandValue());
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

  function syncStandardOptionState() {
    options.forEach((option) => {
      const isSelected = option.classList.contains("is-selected");
      option.setAttribute("aria-pressed", String(isSelected));
    });
  }

  function toggleStandardOption(option) {
    option.classList.toggle("is-selected");
    syncStandardOptionState();
  }

  function setCustomSelected() {
    if (!customWrap) return;
    const hasCustom = Boolean(customInput && customInput.value.trim());
    customWrap.classList.toggle("is-selected", hasCustom);
  }

  function setOtherSelected() {
    if (!otherBtn) return;
    const hasSelected = selectedPopupBrands.length > 0;
    otherBtn.classList.toggle("is-selected", hasSelected);
    otherBtn.setAttribute("aria-pressed", String(hasSelected));
  }

  function syncModalItems() {
    modalItems.forEach((item) => {
      const brand = item.dataset.brand || "";
      item.classList.toggle("is-selected", draftPopupBrands.includes(brand));
    });
  }

  function openBrandModal() {
    if (!modal) return;
    draftPopupBrands = [...selectedPopupBrands];
    syncModalItems();
    modal.hidden = false;
    document.body.style.overflow = "hidden";
  }

  function closeBrandModal() {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = "";
  }

  function applyPopupSelection() {
    selectedPopupBrands = [...draftPopupBrands];
    if (customInput) {
      customInput.value = selectedPopupBrands.join(", ");
    }
    setCustomSelected();
    setOtherSelected();
    closeBrandModal();
  }

  if (otherBtn) {
    otherBtn.addEventListener("click", openBrandModal);
  }

  options.forEach((option) => {
    option.addEventListener("click", () => {
      toggleStandardOption(option);
    });
  });

  if (customInput) {
    customInput.addEventListener("input", () => {
      setCustomSelected();
    });
  }

  modalItems.forEach((item) => {
    item.addEventListener("click", () => {
      const brand = item.dataset.brand || "";
      if (!brand) return;
      const index = draftPopupBrands.indexOf(brand);
      if (index >= 0) {
        draftPopupBrands.splice(index, 1);
      } else {
        draftPopupBrands.push(brand);
      }
      syncModalItems();
    });
  });

  modalCloseButtons.forEach((button) => {
    button.addEventListener("click", closeBrandModal);
  });

  if (modalAcceptBtn) {
    modalAcceptBtn.addEventListener("click", applyPopupSelection);
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      window.location.href = paths.quizProblemUrl();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      if (!hasBrandSelection()) {
        openRequiredModal();
        return;
      }
      if (quiz) {
        quiz.setBrand(collectBrandValue());
      }
      window.location.href = paths.quizContactUrl();
    });
  }

  syncStandardOptionState();
  setOtherSelected();
  setCustomSelected();
})();
