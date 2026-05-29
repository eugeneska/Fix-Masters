(function () {
  const paths = window.FixMastersPaths || {
    requestUrl: () => "/pages/request.html",
    quizStep3TvUrl: () => "/pages/quiz-step-3-tv.html",
    quizStep2Url: () => "/pages/quiz-step-2.html",
  };
  const device = sessionStorage.getItem("fixMastersQuizDevice");
  if (device === "pc") {
    window.location.replace(paths.requestUrl());
    return;
  }
  if (device === "tv") {
    window.location.replace(paths.quizStep3TvUrl());
    return;
  }

  const options = Array.from(document.querySelectorAll(".quiz-step2__choices .quiz-step2-option"));
  const customWrap = document.getElementById("quiz-step3-selected");
  const customInput = customWrap ? customWrap.querySelector(".quiz-step2__custom-input") : null;
  const otherBtn = document.getElementById("quiz-step3-other-btn");
  const prevBtn = document.querySelector(".quiz-step2__btn--prev");
  const nextBtn = document.querySelector(".quiz-step2__btn--next");
  const modal = document.getElementById("brand-modal");
  const modalItems = Array.from(document.querySelectorAll(".quiz-modal__item"));
  const modalCloseButtons = Array.from(document.querySelectorAll("[data-modal-close], [data-modal-cancel]"));
  const modalAcceptBtn = document.querySelector("[data-modal-accept]");
  const requiredModal = document.getElementById("quiz-step3-required-modal");
  const requiredCloseButtons = Array.from(document.querySelectorAll("[data-step3-required-close]"));
  if (!options.length && !customInput && !otherBtn && !prevBtn && !nextBtn && !modal) return;

  function hasBrandSelection() {
    const hasGridSelection = options.some((option) => option.classList.contains("is-selected"));
    const hasCustomBrand = Boolean(customInput && customInput.value.trim());
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

  let selectedPopupBrands = [];
  let draftPopupBrands = [];

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

  function syncCustomInputFromPopup() {
    if (!customInput) return;
    const value = customInput.value.trim();
    const popupValue = selectedPopupBrands.join(", ");
    if (value === popupValue) return;
    selectedPopupBrands = [];
    setOtherSelected();
  }

  function onCustomInputChange() {
    syncCustomInputFromPopup();
    setCustomSelected();
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

  function openModal() {
    if (!modal) return;
    draftPopupBrands = [...selectedPopupBrands];
    syncModalItems();
    modal.hidden = false;
    document.body.style.overflow = "hidden";
  }

  function closeModal() {
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
    closeModal();
  }

  if (otherBtn) {
    otherBtn.addEventListener("click", openModal);
  }

  options.forEach((option) => {
    option.addEventListener("click", () => {
      toggleStandardOption(option);
    });
  });

  if (customInput) {
    customInput.addEventListener("input", onCustomInputChange);
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
    button.addEventListener("click", closeModal);
  });

  if (modalAcceptBtn) {
    modalAcceptBtn.addEventListener("click", applyPopupSelection);
  }

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && modal && !modal.hidden) {
      closeModal();
    }
  });

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

  syncStandardOptionState();
  setOtherSelected();
  setCustomSelected();
})();
