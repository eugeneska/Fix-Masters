(function () {
  const PREFIX = "+375 (";

  function extractNationalDigits(value) {
    let digits = String(value || "").replace(/\D/g, "");
    if (digits.startsWith("375")) {
      digits = digits.slice(3);
    }
    return digits.slice(0, 9);
  }

  function formatDigits(digits) {
    if (!digits.length) {
      return PREFIX;
    }

    let result = "+375 (";
    result += digits.slice(0, 2);

    if (digits.length <= 2) {
      return result;
    }

    result += ") ";
    result += digits.slice(2, 5);

    if (digits.length <= 5) {
      return result;
    }

    result += "-";
    result += digits.slice(5, 7);

    if (digits.length <= 7) {
      return result;
    }

    result += "-";
    result += digits.slice(7, 9);
    return result;
  }

  function countDigitsBefore(formatted, caret) {
    return extractNationalDigits(formatted.slice(0, Math.max(0, caret))).length;
  }

  function caretAfterDigits(digitCount) {
    return formatDigits("9".repeat(digitCount)).length;
  }

  function setCaret(input, pos) {
    requestAnimationFrame(() => {
      const safePos = Math.min(pos, input.value.length);
      input.setSelectionRange(safePos, safePos);
    });
  }

  function updateValidity(input, digits) {
    if (digits.length === 9) {
      input.setCustomValidity("");
      return;
    }
    if (input.required) {
      input.setCustomValidity("Введите номер телефона полностью");
    } else {
      input.setCustomValidity("");
    }
  }

  function render(input, digits, caretDigitIndex) {
    const formatted = formatDigits(digits);
    input.value = formatted;
    input.dataset.phoneDigits = digits;
    updateValidity(input, digits);

    const index =
      typeof caretDigitIndex === "number"
        ? Math.max(0, Math.min(caretDigitIndex, digits.length))
        : digits.length;
    setCaret(input, caretAfterDigits(index));
  }

  function onFocus(event) {
    const input = event.target;
    const digits = input.dataset.phoneDigits || extractNationalDigits(input.value);
    render(input, digits, digits.length);
  }

  function onInput(event) {
    const input = event.target;
    const digits = extractNationalDigits(input.value);
    render(input, digits, digits.length);
  }

  function removeDigits(input, start, end) {
    const digits = input.dataset.phoneDigits || extractNationalDigits(input.value);
    if (!digits.length) {
      return;
    }

    const removeFrom = countDigitsBefore(input.value, Math.min(start, end));
    const removeTo = countDigitsBefore(input.value, Math.max(start, end));
    const nextDigits = digits.slice(0, removeFrom) + digits.slice(removeTo);
    render(input, nextDigits, removeFrom);
  }

  function onKeydown(event) {
    const input = event.target;
    const start = input.selectionStart ?? 0;
    const end = input.selectionEnd ?? 0;

    if (event.key === "Backspace") {
      if (start <= PREFIX.length && end <= PREFIX.length) {
        event.preventDefault();
        return;
      }
      event.preventDefault();
      const digits = input.dataset.phoneDigits || extractNationalDigits(input.value);
      if (!digits.length) {
        render(input, "", 0);
        return;
      }

      if (start !== end) {
        removeDigits(input, start, end);
        return;
      }

      const removeIndex = Math.max(0, countDigitsBefore(input.value, start) - 1);
      const nextDigits = digits.slice(0, removeIndex) + digits.slice(removeIndex + 1);
      render(input, nextDigits, removeIndex);
      return;
    }

    if (event.key === "Delete") {
      if (start < PREFIX.length) {
        event.preventDefault();
        return;
      }
      if (start !== end) {
        event.preventDefault();
        removeDigits(input, start, end);
        return;
      }

      const digits = input.dataset.phoneDigits || extractNationalDigits(input.value);
      const removeIndex = countDigitsBefore(input.value, start);
      if (removeIndex >= digits.length) {
        return;
      }
      event.preventDefault();
      const nextDigits = digits.slice(0, removeIndex) + digits.slice(removeIndex + 1);
      render(input, nextDigits, removeIndex);
      return;
    }

    if (event.key.length === 1 && /\D/.test(event.key)) {
      event.preventDefault();
    }
  }

  function onPaste(event) {
    event.preventDefault();
    const input = event.target;
    const pasted = (event.clipboardData || window.clipboardData).getData("text");
    const digits = extractNationalDigits(pasted);
    render(input, digits, digits.length);
  }

  function onBlur(event) {
    const input = event.target;
    const digits = input.dataset.phoneDigits || extractNationalDigits(input.value);
    if (!digits.length) {
      input.value = "";
      delete input.dataset.phoneDigits;
      input.setCustomValidity("");
    }
  }

  function init(input) {
    if (!input || input.dataset.phoneMaskInit === "true") {
      return input;
    }

    input.dataset.phoneMaskInit = "true";
    input.type = "tel";
    input.inputMode = "tel";
    input.autocomplete = "tel";
    input.placeholder = "+375 (__) ___-__-__";

    input.addEventListener("focus", onFocus);
    input.addEventListener("input", onInput);
    input.addEventListener("keydown", onKeydown);
    input.addEventListener("paste", onPaste);
    input.addEventListener("blur", onBlur);

    const digits = extractNationalDigits(input.value);
    if (digits.length) {
      render(input, digits, digits.length);
    }

    return input;
  }

  function initAll(root) {
    const scope = root && root.querySelectorAll ? root : document;
    scope.querySelectorAll('input[type="tel"]').forEach(init);
  }

  function isComplete(input) {
    return (input.dataset.phoneDigits || extractNationalDigits(input.value)).length === 9;
  }

  function bindForms(root) {
    const scope = root && root.querySelectorAll ? root : document;
    scope.querySelectorAll("form").forEach((form) => {
      if (!form.querySelector('input[type="tel"]') || form.dataset.phoneFormInit === "true") {
        return;
      }
      form.dataset.phoneFormInit = "true";
      form.addEventListener("submit", (event) => {
        let valid = true;
        form.querySelectorAll('input[type="tel"]').forEach((input) => {
          if (!isComplete(input)) {
            input.setCustomValidity("Введите номер телефона полностью");
            valid = false;
          }
        });
        if (!valid) {
          event.preventDefault();
          form.reportValidity();
        }
      });
    });
  }

  function boot() {
    initAll(document);
    bindForms(document);
  }

  window.FixMastersPhoneMask = {
    init,
    initAll,
    bindForms,
    isComplete,
    extractNationalDigits,
    formatDigits,
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
