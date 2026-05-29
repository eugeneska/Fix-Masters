(function () {
  const SOURCE_KEY = "fixMastersLeadSource";
  const ATTRIBUTION_KEY = "fixMastersAttribution";
  const POPUP_SHOWN_KEY = "fixMastersPopupShown";

  const UTM_PARAMS = ["utm_source", "utm_medium", "utm_campaign", "utm_content", "utm_term"];
  const AD_PARAMS = ["gclid", "yclid"];

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

    if (changed) {
      writeAttribution(stored);
    }
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

    async submit({ source, name, phone, comment, quizAnswers, consent }) {
      const paths = window.FixMastersPaths || {};
      const url = paths.leadsStoreUrl || "/api/leads";
      const attribution = readAttribution();

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
