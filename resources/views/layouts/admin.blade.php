<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Админка') — FIX-MASTERS</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
  @stack('head')
</head>
<body class="admin-body">
  @auth
    <header class="admin-header">
      <div class="admin-header__inner">
        <a href="{{ route('admin.leads.index') }}" class="admin-logo">FIX-MASTERS · Админка</a>
        <nav class="admin-nav">
          <a href="{{ route('admin.leads.index') }}" @class(['admin-nav__link', 'is-active' => request()->routeIs('admin.leads.*')])>Заявки</a>
          <a href="{{ route('admin.export.csv', request()->query()) }}" class="admin-nav__link">Выгрузка CSV</a>
          <a href="{{ route('admin.settings.telegram') }}" @class(['admin-nav__link', 'is-active' => request()->routeIs('admin.settings.*')])>Telegram</a>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}" class="admin-logout-form">
          @csrf
          <button type="submit" class="admin-btn admin-btn--ghost">Выйти</button>
        </form>
      </div>
    </header>
  @endauth

  <main class="admin-main">
    @if (session('status'))
      <div class="admin-alert admin-alert--success">{{ session('status') }}</div>
    @endif
    @yield('content')
  </main>

  <script>
    window.FixMastersAdmin = {
      csrfToken: @json(csrf_token()),
      routes: {
        leadUpdate: @json(url('/admin/leads/__ID__')),
      },
    };
  </script>
  <script src="{{ asset('assets/js/admin-leads.js') }}"></script>
  @stack('scripts')
</body>
</html>
