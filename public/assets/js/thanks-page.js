(function () {
  const PENDING_ANALYTICS_KEY = "fixMastersPendingAnalytics";

  sessionStorage.removeItem("fixMastersLeadSubmitted");

  function flushPendingLeadAnalytics() {
    let raw;
    try {
      raw = sessionStorage.getItem(PENDING_ANALYTICS_KEY);
    } catch {
      return;
    }
    if (!raw) return;

    sessionStorage.removeItem(PENDING_ANALYTICS_KEY);

    let pending;
    try {
      pending = JSON.parse(raw);
    } catch {
      return;
    }

    const analytics = window.FixMastersAnalytics;
    if (!analytics) return;

    const formId = pending.formId || pending.source || "unknown_form";
    const extra = {
      lead_id: pending.leadId,
      form_source: pending.source,
      page: window.location.pathname,
    };

    analytics.trackFormSubmit(formId, extra);
    analytics.push("lead_success", { form_id: formId, ...extra }, { skipMetrika: true });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", flushPendingLeadAnalytics);
  } else {
    flushPendingLeadAnalytics();
  }

  const homeBtn = document.getElementById("thanks-home-btn");
  if (!homeBtn) return;

  const paths = window.FixMastersPaths || {};
  const homeUrl = paths.indexUrl || "/";

  homeBtn.addEventListener("click", () => {
    window.location.href = homeUrl;
  });
})();
