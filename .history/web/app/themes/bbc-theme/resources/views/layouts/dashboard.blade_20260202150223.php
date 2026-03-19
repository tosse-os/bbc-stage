<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {!! wp_head() !!}
  @vite(['resources/css/app-dashboard.css','resources/js/dashboard.js'])
</head>

<body class="dashboard text-slate-900">

  <div class="dashboard-layout">

    @if (is_user_logged_in())
    <aside class="dashboard-sidebar w-72 flex flex-col text-white">
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
