{{--
  Auth-Layout
  Wird ausschließlich für Login- und Registrierungsseiten verwendet.
  Enthält KEINE Dashboard-Logik und KEINE User-Abhängigkeiten.
--}}
<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {!! wp_head() !!}

  @vite('resources/css/app-dashboard.css')
</head>

<body class="min-h-screen bg-dashboard-bg flex items-center justify-center">
  @yield('content')
  {!! wp_footer() !!}
</body>

</html>
