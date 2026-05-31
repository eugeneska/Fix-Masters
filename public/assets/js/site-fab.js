(function () {
  if (document.body.classList.contains("quiz-page")) {
    const existingFab = document.getElementById("site-fab");
    if (existingFab) existingFab.remove();
    return;
  }

  const paths = window.FixMastersPaths || { quizDeviceUrl: () => "/quiz/device" };
  const leads = window.FixMastersLeads;
  const INITIAL_DELAY_MS = 20000;
  const EXPANDED_MS = 10000;
  const REPEAT_DELAY_MS = 30000;

  const fab = document.getElementById("site-fab");
  if (!fab) return;

  const btn = fab.querySelector(".site-fab__btn");
  if (!btn) return;

  fab.querySelector(".site-fab__collapse")?.remove();

  let timerId = null;
  let isExpanded = false;

  function clearTimer() {
    if (timerId !== null) {
      window.clearTimeout(timerId);
      timerId = null;
    }
  }

  function expand() {
    if (isExpanded) return;
    isExpanded = true;
    fab.classList.add("is-expanded");
    if (window.FixMastersAnalytics) {
      window.FixMastersAnalytics.trackFab("expand");
    }
  }

  function collapse() {
    if (!isExpanded) return;
    isExpanded = false;
    fab.classList.remove("is-expanded");
  }

  function scheduleCollapse() {
    clearTimer();
    timerId = window.setTimeout(() => {
      collapse();
      scheduleExpand();
    }, EXPANDED_MS);
  }

  function scheduleExpand() {
    clearTimer();
    timerId = window.setTimeout(() => {
      expand();
      scheduleCollapse();
    }, REPEAT_DELAY_MS);
  }

  function startCycle() {
    clearTimer();
    timerId = window.setTimeout(() => {
      expand();
      scheduleCollapse();
    }, INITIAL_DELAY_MS);
  }

  function scheduleHoverCollapse(delay) {
    clearTimer();
    timerId = window.setTimeout(() => {
      collapse();
      scheduleExpand();
    }, delay);
  }

  function onFabEnter() {
    clearTimer();
    expand();
  }

  function onFabLeave() {
    if (!fab.matches(":hover") && !fab.matches(":focus-within")) {
      scheduleHoverCollapse(800);
    }
  }

  fab.addEventListener("mouseenter", onFabEnter);
  fab.addEventListener("mouseleave", onFabLeave);

  fab.addEventListener("focusin", onFabEnter);
  fab.addEventListener("focusout", () => {
    window.setTimeout(onFabLeave, 0);
  });

  btn.addEventListener("click", () => {
    if (window.FixMastersAnalytics) {
      window.FixMastersAnalytics.trackFab("click");
    }
    if (leads) {
      leads.setSource(leads.SOURCES.fab);
    }
    if (window.FixMastersQuiz) {
      window.FixMastersQuiz.reset();
    }
    window.location.href = paths.quizDeviceUrl();
  });

  if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    startCycle();
  }
})();

(function () {
  const toggleButtons = document.querySelectorAll(".services__toggle");
  if (!toggleButtons.length) return;

  const mobileQuery = window.matchMedia("(max-width: 600px)");
  const CHAR_LIMIT = 100;

  function trimToChars(text, limit) {
    const normalized = text.replace(/\s+/g, " ").trim();
    if (normalized.length <= limit) {
      return { short: normalized, full: normalized, truncated: false };
    }
    return {
      short: `${normalized.slice(0, limit)}...`,
      full: normalized,
      truncated: true,
    };
  }

  function applyCollapsedState(card, shouldCollapse) {
    const textEl = card.querySelector(".services__card-text");
    const button = card.querySelector(".services__toggle");
    if (!textEl || !button) return;

    if (!textEl.dataset.fullText) {
      const { short, full, truncated } = trimToChars(textEl.textContent || "", CHAR_LIMIT);
      textEl.dataset.fullText = full;
      textEl.dataset.shortText = short;
      textEl.dataset.truncated = String(truncated);
    }

    const truncated = textEl.dataset.truncated === "true";
    if (!truncated) {
      button.style.visibility = "hidden";
      button.style.pointerEvents = "none";
      button.setAttribute("aria-expanded", "true");
      return;
    }

    button.style.visibility = "";
    button.style.pointerEvents = "";
    if (shouldCollapse) {
      card.classList.remove("is-expanded");
      textEl.textContent = textEl.dataset.shortText || textEl.textContent;
      button.setAttribute("aria-expanded", "false");
    } else {
      card.classList.add("is-expanded");
      textEl.textContent = textEl.dataset.fullText || textEl.textContent;
      button.setAttribute("aria-expanded", "true");
    }
  }

  function syncCardsForViewport() {
    const shouldCollapse = mobileQuery.matches;
    document.querySelectorAll(".services__card").forEach((card) => {
      applyCollapsedState(card, shouldCollapse);
    });
  }

  syncCardsForViewport();
  mobileQuery.addEventListener("change", syncCardsForViewport);

  toggleButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (!mobileQuery.matches) return;
      const card = button.closest(".services__card");
      if (!card) return;
      const textEl = card.querySelector(".services__card-text");
      if (!textEl || textEl.dataset.truncated !== "true") return;

      const isExpanded = card.classList.toggle("is-expanded");
      textEl.textContent = isExpanded
        ? (textEl.dataset.fullText || textEl.textContent)
        : (textEl.dataset.shortText || textEl.textContent);
      button.setAttribute("aria-expanded", String(isExpanded));
    });
  });
})();
