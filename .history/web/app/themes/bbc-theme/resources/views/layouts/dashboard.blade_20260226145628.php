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
$collapsed = is_user_logged_in()
? get_user_meta(get_current_user_id(), 'dashboard_sidebar_collapsed', true) === '1'
: false;
@endphp

<body class="dashboard {{ $collapsed ? 'sidebar-collapsed' : '' }}">

  <div class="dashboard-layout">

    <aside class="dashboard-sidebar hidden md:flex flex-col">
      @include('dashboard.partials.sidebar')
    </aside>

    <main class="dashboard-content flex-1">
      @yield('content')
    </main>

  </div>

  @include('dashboard.partials.mobile-navigation')

  {!! wp_footer() !!}

  {{-- VIDEO MODAL --}}
  <div id="videoModal"
    class="fixed inset-0 z-50 hidden items-center justify-center">

    {{-- Overlay --}}
    <div id="videoOverlay"
      class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>

    {{-- Dialog --}}
    <div id="videoDialog"
      class="relative z-10 w-full max-w-5xl mx-auto px-4 opacity-0 scale-95 transition-all duration-300">

      <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl">

        <button id="videoClose"
          type="button"
          class="absolute top-3 right-3 z-20 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 transition text-white flex items-center justify-center">
          ✕
        </button>

        <div class="aspect-video">
          <iframe id="videoFrame"
            class="w-full h-full"
            src=""
            frameborder="0"
            allow="autoplay; encrypted-media; fullscreen"
            allowfullscreen>
          </iframe>
        </div>

      </div>
    </div>
  </div>

</body>

</html>
