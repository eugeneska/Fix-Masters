(function () {
  const config = window.FixMastersAdmin || {};
  const csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

  function route(template, id) {
    return template.replace("__ID__", String(id));
  }

  async function patchLead(id, body) {
    const response = await fetch(route(config.routes.leadUpdate, id), {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": csrfToken,
      },
      body: JSON.stringify(body),
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
      throw new Error(data.message || "Ошибка сохранения");
    }

    return data;
  }

  function updateStatusBadge(cell, status) {
    const badge = cell.querySelector("[data-status-badge]");
    if (!badge) return;

    badge.textContent = status === "yes" ? "Да" : status === "no" ? "Нет" : "—";
    badge.classList.remove("status-yes", "status-no", "status-empty");
    badge.classList.add(status === "yes" ? "status-yes" : status === "no" ? "status-no" : "status-empty");
  }

  function updateSegmented(cell, status) {
    cell.querySelectorAll(".admin-segmented__btn").forEach((btn) => {
      const value = btn.dataset.status ?? "";
      btn.classList.toggle("is-active", value === (status || ""));
    });
  }

  function updateAnalyticsDots(leadId, lead) {
    document.querySelectorAll(`.admin-analytics-dots[data-lead-id="${leadId}"]`).forEach((block) => {
      const metrika = block.querySelector("[data-metrika-dot]");
      const ga4 = block.querySelector("[data-ga4-dot]");
      if (metrika) {
        metrika.classList.toggle("is-sent", !!lead.metrika_conversion_sent);
        metrika.title = lead.metrika_conversion_sent ? "Отправлено" : "Не отправлено";
      }
      if (ga4) {
        ga4.classList.toggle("is-sent", !!lead.ga4_conversion_sent);
        ga4.title = lead.ga4_conversion_sent ? "Отправлено" : "Не отправлено";
      }
    });
  }

  function notifyConversionResult(conversion) {
    if (!conversion) return;

    const errors = conversion.errors || [];
    const sent = [];
    if (conversion.ga4) sent.push("GA4");
    if (conversion.metrika) sent.push("Яндекс Метрика");

    if (sent.length && !errors.length) {
      alert(`Конверсия «Качественный лид» отправлена: ${sent.join(", ")}.`);
      return;
    }

    if (sent.length && errors.length) {
      alert(`Частичная отправка: ${sent.join(", ")}.\n\n${errors.join("\n")}`);
      return;
    }

    if (errors.length) {
      alert(errors.join("\n"));
    }
  }

  document.querySelectorAll(".admin-table__row[data-href]").forEach((row) => {
    row.addEventListener("click", () => {
      window.location.href = row.dataset.href;
    });
  });

  const selectAll = document.querySelector("[data-select-all]");
  if (selectAll) {
    selectAll.addEventListener("change", () => {
      document.querySelectorAll("[data-lead-select]").forEach((checkbox) => {
        checkbox.checked = selectAll.checked;
      });
    });

    selectAll.addEventListener("click", (event) => {
      event.stopPropagation();
    });
  }

  document.querySelectorAll("[data-lead-select]").forEach((checkbox) => {
    checkbox.addEventListener("click", (event) => {
      event.stopPropagation();
    });
  });

  document.querySelectorAll(".admin-delete-form").forEach((form) => {
    form.addEventListener("submit", (event) => {
      if (!window.confirm("Удалить заявку? Это действие нельзя отменить.")) {
        event.preventDefault();
      }
    });
  });

  document.addEventListener("click", async (event) => {
    const btn = event.target.closest(".admin-segmented__btn");
    if (!btn) return;

    event.preventDefault();
    event.stopPropagation();

    const cell = btn.closest(".admin-status-cell");
    if (!cell) return;

    const leadId = cell.dataset.leadId;
    const field = cell.dataset.field;
    const status = btn.dataset.status ?? "";

    const previous = cell.querySelector(".admin-segmented__btn.is-active")?.dataset.status ?? "";

    try {
      updateSegmented(cell, status);
      updateStatusBadge(cell, status);

      const body = {};
      body[field] = status;

      const data = await patchLead(leadId, body);

      if (data.lead) {
        updateAnalyticsDots(leadId, data.lead);
      }

      if (field === "quality_status" && status === "yes") {
        notifyConversionResult(data.conversion);
      }
    } catch (error) {
      updateSegmented(cell, previous);
      updateStatusBadge(cell, previous);
      alert(error.message);
    }
  });

  document.querySelectorAll(".admin-note-form").forEach((form) => {
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      event.stopPropagation();

      const leadId = form.dataset.leadId;
      const input = form.querySelector('[name="admin_note"]');

      try {
        await patchLead(leadId, { admin_note: input?.value ?? "" });
      } catch (error) {
        alert(error.message);
      }
    });
  });

  const saveNoteBtn = document.querySelector("[data-save-note]");
  if (saveNoteBtn) {
    saveNoteBtn.addEventListener("click", async () => {
      const panel = saveNoteBtn.closest("[data-lead-id]");
      const leadId = panel?.dataset.leadId;
      const textarea = panel?.querySelector("[data-note-input]");

      if (!leadId) return;

      try {
        await patchLead(leadId, { admin_note: textarea?.value ?? "" });
        saveNoteBtn.textContent = "Сохранено";
        setTimeout(() => {
          saveNoteBtn.textContent = "Сохранить комментарий";
        }, 2000);
      } catch (error) {
        alert(error.message);
      }
    });
  }
})();
