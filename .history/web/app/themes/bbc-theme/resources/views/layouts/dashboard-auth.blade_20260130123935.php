<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {!! wp_head() !!}
  @vite(['resources/css/app-dashboard.css','resources/js/dashboard.js'])
</head>

<body class="dashboard-login min-h-screen flex items-center justify-center text-slate-100 bg-slate-950">

  @yield('content')

  {!! wp_footer() !!}
</body>

</html>
