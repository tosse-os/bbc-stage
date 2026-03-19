<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {!! wp_head() !!}
</head>

<body class="bg-slate-100 text-slate-900">

  <div class="flex min-h-screen">

    @if (is_user_logged_in())
    @include('dashboard.partials.sidebar')
    @endif

    <main class="flex-1 px-10 py-8">
      @yield('content')
    </main>

  </div>

  {!! wp_footer() !!}
</body>

</html>
