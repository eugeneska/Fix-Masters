<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'FIX-MASTERS')</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300&family=Syne:wght@400..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  @include('partials.analytics')
  @stack('head')
</head>
<body @hasSection('bodyClass') class="@yield('bodyClass')" @endif>
  @include('partials.header')

  @yield('content')

  @include('partials.footer')

  @hasSection('showQuizPromo')
    @include('partials.quiz-promo-modal')
  @endif

  @include('partials.site-fab')

  @include('partials.paths-config')
  <script src="{{ asset('assets/js/quiz-storage.js') }}"></script>
  <script src="{{ asset('assets/js/lead-tracking.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
