<!doctype html>
<html @php(language_attributes())>

<head>
  @include('partials.head')
</head>

<body @php(body_class())>

  {{-- Header --}}
  @include('partials.header')

  <main id="app">
    @yield('content')
  </main>

  {{-- Conversion Footer --}}
  @include('partials.conversion-footer')

  @php(wp_footer())
</body>

</html>
