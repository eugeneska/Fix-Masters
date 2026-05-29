(function () {
  if (document.body.classList.contains("quiz-page")) return;

  const paths = window.FixMastersPaths || {
    quizDeviceUrl: function () {
      return "/quiz/device";
    },
  };
  const leads = window.FixMastersLeads;

  const FIRST_SHOW_DELAY_MS = 30000;
  const MIN_ON_PAGE_MS = 30000;
  const EXIT_TOP_EDGE_PX = 8;

  const modal = document.getElementById("quiz-promo-modal");
  if (!modal) return;

  const closeTargets = Array.from(modal.querySelectorAll("[data-quiz-promo-close]"));
  const ctaButton = modal.querySelector(".quiz-promo-modal__btn");
  const pageLoadedAt = Date.now();
  let popupShown = leads ? leads.wasPopupShown() : false;

  function isOpen() {
    return !modal.hidden;
  }

  function lockBody(lock) {
    document.body.style.overflow = lock ? "hidden" : "";
  }

  function markShown() {
    popupShown = true;
    if (leads) {
      leads.markPopupShown();
    }
  }

  function openModal() {
    if (popupShown || isOpen()) return false;
    modal.hidden = false;
    lockBody(true);
    markShown();
    return true;
  }

  function closeModal() {
    modal.hidden = true;
    lockBody(false);
  }

  function tryOpenFromTimer() {
    openModal();
  }

  function tryOpenFromExitIntent() {
    if (popupShown) return;
    if (Date.now() - pageLoadedAt < MIN_ON_PAGE_MS) return;
    openModal();
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
      if (leads) {
        leads.setSource(leads.SOURCES.popup);
      }
      if (window.FixMastersQuiz) {
        window.FixMastersQuiz.reset();
      }
      window.location.href = paths.quizDeviceUrl();
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
