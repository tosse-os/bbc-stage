<!doctype html>
@php
$theme = 'light';

if (is_user_logged_in()) {
  $stored = get_user_meta(get_current_user_id(), USER_META_THEME, true);
  $theme = $stored === 'dark' ? 'dark' : 'light';
}
@endphp

<html {!! get_language_attributes() !!} data-theme="{{ $theme }}">


<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {!! wp_head() !!}
  @vite([
  'resources/css/dashboard-app.css',
  'resources/js/dashboard.js',
  'resources/js/theme-toggle.js'
  ])
  <meta name="theme-color" content="#40889e">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>

@php
$sidebar = is_user_logged_in()
? get_user_meta(get_current_user_id(), 'dashboard_sidebar_state', true)
: '';
@endphp

<body class="dashboard {{ $sidebar === 'collapsed' ? 'sidebar-collapsed' : '' }}">

  <div class="dashboard-layout">

    @if (is_user_logged_in())
    <aside class="dashboard-sidebar flex flex-col text-white">
      @include('dashboard.partials.sidebar')
    </aside>
    @endif

    <main class="dashboard-content flex-1 px-10 py-8">
      @yield('content')
    </main>

  </div>

  {!! wp_footer() !!}
</body>

</html>
