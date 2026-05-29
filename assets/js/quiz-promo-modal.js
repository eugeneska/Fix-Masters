(function () {
  if (document.body.classList.contains("quiz-page")) return;

  const paths = window.FixMastersPaths || {
    quizUrl: function () {
      return "/pages/quiz.html";
    },
  };

  const FIRST_SHOW_DELAY_MS = 30000;
  const COOLDOWN_MS = 30000;
  const EXIT_TOP_EDGE_PX = 8;

  const modal = document.getElementById("quiz-promo-modal");
  if (!modal) return;

  const closeTargets = Array.from(modal.querySelectorAll("[data-quiz-promo-close]"));
  const ctaButton = modal.querySelector(".quiz-promo-modal__btn");
  let lastPopupAt = 0;
  let timerTriggered = false;
  let exitIntentTriggered = false;

  function isOpen() {
    return !modal.hidden;
  }

  function canShow() {
    if (isOpen()) return false;
    if (lastPopupAt && Date.now() - lastPopupAt < COOLDOWN_MS) return false;
    return true;
  }

  function lockBody(lock) {
    document.body.style.overflow = lock ? "hidden" : "";
  }

  function openModal() {
    if (!canShow()) return false;
    modal.hidden = false;
    lockBody(true);
    lastPopupAt = Date.now();
    return true;
  }

  function closeModal() {
    modal.hidden = true;
    lockBody(false);
    lastPopupAt = Date.now();
  }

  function tryOpenFromTimer() {
    if (timerTriggered) return;
    timerTriggered = true;
    openModal();
  }

  function tryOpenFromExitIntent() {
    if (exitIntentTriggered) return;
    if (!canShow()) return;
    if (openModal()) {
      exitIntentTriggered = true;
    }
  }

  function isLeavingFromTop(event) {
    if (event.clientY > EXIT_TOP_EDGE_PX) return false;

    const related = event.relatedTarget || event.toElement;
    if (!related) return true;
    if (related === document.documentElement || related === document || related === document.body) {
      return true;
    }

    return false;
  }

  function handleExitIntent(event) {
    if (!isLeavingFromTop(event)) return;
    tryOpenFromExitIntent();
  }

  closeTargets.forEach((el) => {
    el.addEventListener("click", closeModal);
  });

  if (ctaButton) {
    ctaButton.addEventListener("click", () => {
      sessionStorage.removeItem("fixMastersQuizDevice");
      window.location.href = paths.quizUrl();
    });
  }

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && isOpen()) {
      closeModal();
    }
  });

  document.documentElement.addEventListener("mouseleave", handleExitIntent);
  document.addEventListener("mouseout", handleExitIntent);

  window.setTimeout(tryOpenFromTimer, FIRST_SHOW_DELAY_MS);
})();
