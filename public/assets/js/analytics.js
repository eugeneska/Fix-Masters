(function () {
  window.dataLayer = window.dataLayer || [];

  function getConfig() {
    return window.FixMastersAnalyticsConfig || {};
  }

  function forwardToGa4(eventName, params) {
    const cfg = getConfig();
    if (!cfg.ga4Direct || typeof gtag !== "function") return;
    gtag("event", eventName, params || {});
  }

  function forwardToMetrika(eventName, params) {
    const counterId = getConfig().metrikaId;
    if (!counterId || typeof ym !== "function") return;
    ym(counterId, "reachGoal", eventName, params || {});
  }

  function push(event, params) {
    const payload = { event, ...(params || {}) };
    window.dataLayer.push(payload);
    forwardToGa4(event, params);
    forwardToMetrika(event, params);
  }

  /** Имя события для GTM: латиница, цифры, подчёркивание (как lead source). */
  function normalizeFormEventId(formId) {
    const raw = String(formId || "unknown_form").trim().toLowerCase();
    const safe = raw.replace(/[^a-z0-9_]+/g, "_").replace(/^_|_$/g, "");
    return safe || "unknown_form";
  }

  function readCookie(name) {
    const match = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/[.$?*|{}()[\]\\/+^]/g, "\\$&") + "=([^;]*)"));
    return match ? decodeURIComponent(match[1]) : null;
  }

  function getGaClientId() {
    const ga = readCookie("_ga");
    if (!ga) return null;
    const parts = ga.split(".");
    if (parts.length >= 4) {
      return parts.slice(-2).join(".");
    }
    return ga;
  }

  function getYmClientId() {
    return readCookie("_ym_uid");
  }

  window.FixMastersAnalytics = {
    push,
    getGaClientId,
    getYmClientId,

    trackButtonClick(label, extra) {
      push("button_click", { button_label: label, ...extra });
    },

    trackQuizStart(extra) {
      push("quiz_start", extra || {});
    },

    trackQuizStep(step, extra) {
      push("quiz_step", { quiz_step: step, ...extra });
    },

    /**
     * Успешная отправка формы (ТЗ: event = ID формы, например footer_form).
     * @param {string} formId — идентификатор формы (совпадает с source заявки)
     * @param {Record<string, unknown>} [extra] — lead_id и др.
     */
    trackFormSubmit(formId, extra) {
      const id = normalizeFormEventId(formId);
      push(id, { form_id: id, ...extra });
    },

    trackLeadSuccess(leadId, formId, extra) {
      this.trackFormSubmit(formId || "unknown_form", { lead_id: leadId, ...extra });
    },

    trackPopup(action, extra) {
      push("popup_event", { popup_action: action, ...extra });
    },

    trackFab(action, extra) {
      push("fab_event", { fab_action: action, ...extra });
    },
  };

  document.addEventListener(
    "click",
    (event) => {
      const el = event.target.closest("[data-analytics-event]");
      if (!el) return;
      const eventName = el.dataset.analyticsEvent;
      const label = el.dataset.analyticsLabel || el.textContent?.trim() || "";
      const params = {
        element_label: label,
        button_label: label,
      };
      if (el.dataset.leadSource) {
        params.lead_source = el.dataset.leadSource;
      }
      push(eventName, params);
    },
    true,
  );

  function trackQuizStartIfNeeded() {
    if (window.location.pathname.includes("/quiz/device")) {
      push("quiz_start", { page: window.location.pathname });
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", trackQuizStartIfNeeded);
  } else {
    trackQuizStartIfNeeded();
  }
})();
