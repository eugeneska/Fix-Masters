(function () {
  const paths = window.FixMastersPaths || {
    quizDeviceUrl: () => "/quiz/device",
    quizProblemUrl: () => "/quiz/problem",
  };
  const quiz = window.FixMastersQuiz;
  const leads = window.FixMastersLeads;

  document.querySelectorAll(".pricing__btn[data-quiz-full]").forEach((link) => {
    link.addEventListener("click", (event) => {
      event.preventDefault();
      if (leads) {
        leads.setSource(leads.SOURCES.servicesSurvey);
      }
      if (quiz) {
        quiz.reset();
      }
      window.location.href = paths.quizDeviceUrl();
    });
  });

  const cardSources = {
    laptop: leads ? leads.SOURCES.cardLaptop : "card_laptop",
    pc: leads ? leads.SOURCES.cardPc : "card_pc",
    tv: leads ? leads.SOURCES.cardTv : "card_tv",
  };

  document.querySelectorAll(".services__btn[data-quiz-device]").forEach((button) => {
    button.addEventListener("click", () => {
      const device = button.dataset.quizDevice;
      if (!device) return;
      if (leads && cardSources[device]) {
        leads.setSource(cardSources[device]);
      }
      if (quiz) {
        quiz.setDevice(device);
      }
      window.location.href = paths.quizProblemUrl();
    });
  });
})();
