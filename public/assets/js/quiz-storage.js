(function () {
  const DEVICE_KEY = "fixMastersQuizDevice";
  const DATA_KEY = "fixMastersQuizData";

  const DEVICE_LABELS = {
    laptop: "Ноутбук",
    pc: "Компьютер",
    tv: "Телевизор",
  };

  function readData() {
    try {
      const raw = sessionStorage.getItem(DATA_KEY);
      return raw ? JSON.parse(raw) : {};
    } catch {
      return {};
    }
  }

  function writeData(data) {
    sessionStorage.setItem(DATA_KEY, JSON.stringify(data));
  }

  window.FixMastersQuiz = {
    DEVICE_KEY,
    DATA_KEY,
    DEVICE_LABELS,

    getDevice() {
      return sessionStorage.getItem(DEVICE_KEY) || readData().device || null;
    },

    setDevice(device) {
      if (!device) return;
      sessionStorage.setItem(DEVICE_KEY, device);
      const data = readData();
      data.device = device;
      data.device_label = DEVICE_LABELS[device] || device;
      writeData(data);
    },

    setProblems(problems, problemCustom) {
      const data = readData();
      data.problems = Array.isArray(problems) ? problems : [];
      data.problem_custom = problemCustom || "";
      writeData(data);
    },

    setBrand(brand) {
      const data = readData();
      data.brand = brand || "";
      writeData(data);
    },

    getAnswersForSubmit() {
      const device = this.getDevice();
      const data = readData();
      if (!device && !data.problems && !data.brand) {
        return null;
      }

      return {
        device: device || data.device || null,
        device_label: data.device_label || (device ? DEVICE_LABELS[device] : null),
        problems: data.problems || [],
        problem_custom: data.problem_custom || "",
        brand: data.brand || "",
      };
    },

    reset() {
      sessionStorage.removeItem(DEVICE_KEY);
      sessionStorage.removeItem(DATA_KEY);
    },
  };
})();
