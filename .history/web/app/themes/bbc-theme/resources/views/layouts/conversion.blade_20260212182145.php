<!doctype html>
<html @php(language_attributes())>

<head>
  @include('partials.head')
</head>

<body @php(body_class())>

  {{-- Header --}}
  @include('partials.header')

  {{-- Main Content --}}
  <div id="app">
    @yield('content')
  </div>

  {{-- Conversion Footer --}}
  @include('partials.conversion-footer')

  @php(wp_footer())
</body>

</html>
