<!doctype html>
<html {!! get_language_attributes() !!} data-theme="{{ get_user_meta(get_current_user_id(), 'dashboard_theme', true) ?: 'light' }}">

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @wphead

  @vite([
  'resources/css/app-dashboard.css',
  'resources/js/dashboard.js'
  ])
</head>

<body class="min-h-screen bg-dashboard-bg text-dashboard-text">

  <div class="flex min-h-screen">

    @if (is_user_logged_in())
    @include('dashboard.partials.sidebar')
    @endif


    <div class="flex flex-col flex-1">

      @include('dashboard.partials.topbar')

      <main class="flex-1 px-8 py-6 overflow-x-hidden">
        @yield('content')
      </main>

    </div>

  </div>

  @wpfooter
</body>

</html>
