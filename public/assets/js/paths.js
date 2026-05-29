(function () {
  window.FixMastersPaths = window.FixMastersPaths || {
    base: "",
    url: function (path) {
      if (!path) return "/";
      return path.charAt(0) === "/" ? path : "/" + path;
    },
    indexUrl: "/",
    quizDeviceUrl: function () {
      return "/quiz/device";
    },
    quizProblemUrl: function () {
      return "/quiz/problem";
    },
    quizBrandUrl: function () {
      return "/quiz/brand";
    },
    quizContactUrl: function () {
      return "/quiz/contact";
    },
    thanksUrl: function () {
      return "/thanks";
    },
    leadsStoreUrl: "/api/leads",
    quizUrl: function () {
      return "/quiz/device";
    },
    quizStep2Url: function () {
      return "/quiz/problem";
    },
    quizStep3Url: function () {
      return "/quiz/brand";
    },
    quizStep3TvUrl: function () {
      return "/quiz/brand";
    },
    requestUrl: function () {
      return "/quiz/contact";
    },
    isHomePage: false,
  };
})();
