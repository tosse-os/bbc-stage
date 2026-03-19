{{-- resources/views/layouts/dashboard.blade.php --}}
<!doctype html>
<html {!! get_language_attributes() !!} data-theme="{{ get_user_meta(get_current_user_id(), 'dashboard_theme', true) ?: 'light' }}">

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {!! wp_head() !!}

  @vite([
  'resources/css/app-dashboard.css',
  'resources/js/dashboard.js'
  ])
</head>

<body class="min-h-screen bg-gray-100 text-gray-900">

  <div class="flex min-h-screen">

    @if (is_user_logged_in())
    @include('dashboard.partials.sidebar')
    @endif

    <main class="flex-1 px-12 py-10">
      @yield('content')
    </main>

  </div>

  {!! wp_footer() !!}
</body>



</html>
