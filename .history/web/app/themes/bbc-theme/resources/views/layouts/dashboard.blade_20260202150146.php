<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {!! wp_head() !!}

  @php
  $terms = get_terms([
  'taxonomy' => 'analysis_market',
  'hide_empty' => false,
  ]);

  $assets = [];

  foreach ($terms as $term) {
  if ($term->parent !== 0) {
  $assets[] = [
  'name' => html_entity_decode($term->name),
  'slug' => $term->slug,
  ];
  }
  }
  @endphp

  <script>
    window.AnalysisFilters = {
      !!json_encode([
        'assets' => $assets
      ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!
    };
  </script>


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
