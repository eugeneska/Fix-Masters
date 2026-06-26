(function () {
  sessionStorage.removeItem("fixMastersLeadSubmitted");

  const homeBtn = document.getElementById("thanks-home-btn");
  if (!homeBtn) return;

  const paths = window.FixMastersPaths || {};
  const homeUrl = paths.indexUrl || "/";

  homeBtn.addEventListener("click", () => {
    window.location.href = homeUrl;
  });
})();
