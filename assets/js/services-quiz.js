(function () {
  const QUIZ_DEVICE_KEY = "fixMastersQuizDevice";
  const paths = window.FixMastersPaths || {
    quizUrl: () => "/pages/quiz.html",
    quizStep2Url: () => "/pages/quiz-step-2.html",
  };

  document.querySelectorAll(".pricing__btn[data-quiz-full]").forEach((link) => {
    link.addEventListener("click", (event) => {
      event.preventDefault();
      sessionStorage.removeItem(QUIZ_DEVICE_KEY);
      window.location.href = paths.quizUrl();
    });
  });

  document.querySelectorAll(".services__btn[data-quiz-device]").forEach((button) => {
    button.addEventListener("click", () => {
      const device = button.dataset.quizDevice;
      if (!device) return;
      sessionStorage.setItem(QUIZ_DEVICE_KEY, device);
      window.location.href = paths.quizStep2Url();
    });
  });
})();
