<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {!! wp_head() !!}
  @vite(['resources/css/dashboard-app.css','resources/js/dashboard.js'])
</head>

@php
$sidebar = get_user_meta(get_current_user_id(), 'dashboard_sidebar_state', true);
$collapsed = $sidebar === 'collapsed';
@endphp

<body class="dashboard {{ $collapsed ? 'sidebar-collapsed' : '' }}">


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
