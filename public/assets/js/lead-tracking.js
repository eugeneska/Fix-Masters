(function () {
  const SOURCE_KEY = "fixMastersLeadSource";
  const ATTRIBUTION_KEY = "fixMastersAttribution";
  const POPUP_SHOWN_KEY = "fixMastersPopupShown";
  const SUBMIT_GUARD_KEY = "fixMastersLeadSubmitted";
  const PENDING_ANALYTICS_KEY = "fixMastersPendingAnalytics";

  const UTM_PARAMS = ["utm_source", "utm_medium", "utm_campaign", "utm_content", "utm_term"];
  const AD_PARAMS = ["gclid", "yclid"];
  const FIRST_CONTACT_KEY = "fixMastersFirstContact";

  const SOURCES = {
    headerCallback: "header_callback",
    heroDiagnostic: "hero_diagnostic",
    heroMaster: "hero_master",
    cardLaptop: "card_laptop",
    cardPc: "card_pc",
    cardTv: "card_tv",
    servicesSurvey: "services_survey",
    footerForm: "footer_form",
    fab: "fab",
    popup: "popup",
    quizContact: "quiz_contact",
  };

  function readAttribution() {
    try {
      const raw = localStorage.getItem(ATTRIBUTION_KEY);
      return raw ? JSON.parse(raw) : {};
    } catch {
      return {};
    }
  }

  function writeAttribution(data) {
    localStorage.setItem(ATTRIBUTION_KEY, JSON.stringify(data));
  }

  function captureFromUrl() {
    const params = new URLSearchParams(window.location.search);
    const stored = readAttribution();
    let changed = false;

    [...UTM_PARAMS, ...AD_PARAMS].forEach((key) => {
      const value = params.get(key);
      if (value && stored[key] !== value) {
        stored[key] = value;
        changed = true;
      }
    });

    if (!localStorage.getItem(FIRST_CONTACT_KEY)) {
      localStorage.setItem(FIRST_CONTACT_KEY, window.location.href);
    }

    if (changed) {
      writeAttribution(stored);
    }
  }

  function getTrackingContext() {
    const analytics = window.FixMastersAnalytics || {};
    return {
      form_url: window.location.href,
      first_contact_url: localStorage.getItem(FIRST_CONTACT_KEY) || window.location.href,
      referrer: document.referrer || null,
      last_click: sessionStorage.getItem(SOURCE_KEY) || null,
      ym_client_id: analytics.getYmClientId ? analytics.getYmClientId() : null,
      ga_client_id: analytics.getGaClientId ? analytics.getGaClientId() : null,
    };
  }

  function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
  }

  window.FixMastersLeads = {
    SOURCES,
    SOURCE_KEY,
    POPUP_SHOWN_KEY,

    init() {
      captureFromUrl();
    },

    setSource(source) {
      if (!source) return;
      sessionStorage.setItem(SOURCE_KEY, source);
    },

    getSource() {
      return sessionStorage.getItem(SOURCE_KEY) || SOURCES.quizContact;
    },

    getAttribution() {
      return readAttribution();
    },

    markPopupShown() {
      localStorage.setItem(POPUP_SHOWN_KEY, "1");
    },

    wasPopupShown() {
      return localStorage.getItem(POPUP_SHOWN_KEY) === "1";
    },

    resolveFormEventId(source, formElement) {
      if (formElement?.dataset?.analyticsFormId) {
        return formElement.dataset.analyticsFormId;
      }
      return source || this.getSource();
    },

    async submit({ source, name, phone, comment, quizAnswers, consent, formElement }) {
      if (sessionStorage.getItem(SUBMIT_GUARD_KEY) === "1") {
        throw new Error("Заявка уже отправлена. Обновите страницу, чтобы отправить снова.");
      }

      const paths = window.FixMastersPaths || {};
      const url = paths.leadsStoreUrl || "/api/leads";
      const attribution = readAttribution();

      const tracking = getTrackingContext();

      const body = {
        source: source || this.getSource(),
        name,
        phone,
        comment: comment || null,
        consent: consent ? true : false,
        quiz_answers: quizAnswers || null,
        utm_source: attribution.utm_source || null,
        utm_medium: attribution.utm_medium || null,
        utm_campaign: attribution.utm_campaign || null,
        utm_content: attribution.utm_content || null,
        utm_term: attribution.utm_term || null,
        gclid: attribution.gclid || null,
        yclid: attribution.yclid || null,
        form_url: tracking.form_url,
        first_contact_url: tracking.first_contact_url,
        referrer: tracking.referrer,
        last_click: tracking.last_click,
        ym_client_id: tracking.ym_client_id,
        ga_client_id: tracking.ga_client_id,
        messenger: null,
      };

      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": getCsrfToken(),
        },
        body: JSON.stringify(body),
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        const message = data.message || (data.errors ? Object.values(data.errors).flat().join(" ") : "Ошибка отправки");
        throw new Error(message);
      }

      sessionStorage.setItem(SUBMIT_GUARD_KEY, "1");

      const formEventId = this.resolveFormEventId(source, formElement);
      sessionStorage.setItem(
        PENDING_ANALYTICS_KEY,
        JSON.stringify({
          formId: formEventId,
          leadId: data.id ?? null,
          source: body.source,
        }),
      );

      return data;
    },
  };

  document.addEventListener(
    "click",
    (event) => {
      const el = event.target.closest("[data-lead-source]");
      if (el && el.dataset.leadSource) {
        window.FixMastersLeads.setSource(el.dataset.leadSource);
      }
    },
    true,
  );

  window.FixMastersLeads.init();
})();
