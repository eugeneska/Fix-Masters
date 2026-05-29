(function () {
  const paths = window.FixMastersPaths || {
    quizStep2Url: () => "/pages/quiz-step-2.html",
  };
  const cards = Array.from(document.querySelectorAll(".quiz-card"));
  const nextBtn = document.querySelector(".quiz__next-btn");
  const requiredModal = document.getElementById("quiz-required-modal");
  const requiredCloseButtons = Array.from(document.querySelectorAll("[data-required-close]"));
  if (!cards.length && !nextBtn) return;

  function selectCard(nextCard) {
    cards.forEach((card) => {
      const isSelected = card === nextCard;
      card.classList.toggle("is-selected", isSelected);
      card.setAttribute("aria-checked", String(isSelected));
    });
  }

  cards.forEach((card) => {
    card.addEventListener("click", () => {
      selectCard(card);
    });
  });

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

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      const hasSelection = cards.some((card) => card.classList.contains("is-selected"));
      if (!hasSelection) {
        openRequiredModal();
        return;
      }
      const selectedCard = cards.find((card) => card.classList.contains("is-selected"));
      const device = selectedCard?.dataset.quizOption;
      if (device) {
        sessionStorage.setItem("fixMastersQuizDevice", device);
      }
      window.location.href = paths.quizStep2Url();
    });
  }
})();
