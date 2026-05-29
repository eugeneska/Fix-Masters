<script>
window.FixMastersPaths = {
  base: '',
  url: function (path) {
    if (!path) return '/';
    return path.charAt(0) === '/' ? path : '/' + path;
  },
  indexUrl: @json(route('home')),
  quizDeviceUrl: function () { return @json(route('quiz.device')); },
  quizProblemUrl: function () { return @json(route('quiz.problem')); },
  quizBrandUrl: function () { return @json(route('quiz.brand')); },
  quizContactUrl: function () { return @json(route('quiz.contact')); },
  thanksUrl: function () { return @json(route('thanks')); },
  privacyPolicyUrl: function () { return @json(route('privacy')); },
  leadsStoreUrl: @json(route('leads.store')),
  isHomePage: @json(request()->routeIs('home')),
  quizUrl: function () { return @json(route('quiz.device')); },
  quizStep2Url: function () { return @json(route('quiz.problem')); },
  quizStep3Url: function () { return @json(route('quiz.brand')); },
  quizStep3TvUrl: function () { return @json(route('quiz.brand')); },
  requestUrl: function () { return @json(route('quiz.contact')); },
};
</script>
