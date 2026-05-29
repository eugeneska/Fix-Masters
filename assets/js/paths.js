(function () {
  function detectBasePath() {
    var scripts = document.getElementsByTagName("script");
    var i;
    for (i = 0; i < scripts.length; i++) {
      var src = scripts[i].getAttribute("src");
      if (!src || src.indexOf("paths.js") === -1) continue;
      try {
        return new URL(src, window.location.href).pathname.replace(
          /\/assets\/js\/paths\.js(?:\?.*)?$/,
          ""
        );
      } catch (e) {
        break;
      }
    }
    var path = window.location.pathname || "/";
    var pagesIdx = path.indexOf("/pages/");
    if (pagesIdx !== -1) return path.slice(0, pagesIdx);
    var lastSlash = path.lastIndexOf("/");
    return lastSlash <= 0 ? "" : path.slice(0, lastSlash);
  }

  var base = detectBasePath();

  function url(path) {
    if (!path) return base || "/";
    var normalized = path.charAt(0) === "/" ? path : "/" + path;
    return base + normalized;
  }

  window.FixMastersPaths = {
    base: base,
    url: url,
    indexUrl: url("/"),
    quizUrl: function () {
      return url("/pages/quiz.html");
    },
    quizStep2Url: function () {
      return url("/pages/quiz-step-2.html");
    },
    quizStep3Url: function () {
      return url("/pages/quiz-step-3.html");
    },
    quizStep3TvUrl: function () {
      return url("/pages/quiz-step-3-tv.html");
    },
    requestUrl: function () {
      return url("/pages/request.html");
    },
  };
})();
