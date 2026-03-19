<section class="max-w-7xl">

  {{-- Header --}}
  <header class="mb-6">
    <h1 class="text-2xl font-semibold">
      Analyses Overview
    </h1>
  </header>

  @php
  /**
  * Ermittelt den aktuell gewählten Markt aus der URL
  */
  $currentMarket = request()->get('market');

  /**
  * Lädt ausschließlich Top-Level-Märkte für die Filter-Tabs im Dashboard
  * Untergeordnete Märkte werden implizit über die Taxonomie-Hierarchie abgedeckt
  */
  $markets = get_terms([
  'taxonomy' => 'analysis_market',
  'parent' => 0,
  'hide_empty' => true,
  ]);
  @endphp

  {{-- Markt-Tabs --}}
  {{--
  <div class="flex gap-2 mb-6 text-sm flex-wrap">
    <a
      href="/dashboard"
      class="px-4 py-2 rounded-md border {{ !$currentMarket ? 'bg-white' : 'text-slate-500 hover:text-slate-900' }}">
      All
    </a>

    @foreach ($markets as $market)
    <a
      href="?market={{ $market->slug }}"
      class="px-4 py-2 rounded-md {{ $currentMarket === $market->slug ? 'bg-white border' : 'text-slate-500 hover:text-slate-900' }}">
      {{ $market->name }}
    </a>
    @endforeach
    </div>
  --}}

    @include('dashboard.partials.filters')


    {{-- Tabelle --}}
    <div class="bg-white rounded-xl shadow-sm">

      {{-- Tabellenkopf --}}
      <div class="grid grid-cols-[160px_140px_1fr_140px_160px] px-6 py-4 gap-4 border-b text-sm font-medium text-slate-500 items-center">
        <div>Asset</div>
        <div>Preview</div>
        <div>Description</div>
        <div>Date</div>
        <div></div>
      </div>

      @php
      /**
      * Query für Analysen
      * Filtert nach Markt, falls ein Top-Level-Markt ausgewählt wurde
      * Parent-Terme greifen korrekt, da Untermarkt + Übermarkt immer gemeinsam gesetzt sind
      */
      $args = [
      'post_type' => 'analysis',
      'posts_per_page' => 20,
      'orderby' => 'meta_value',
      'meta_key' => 'publish_date',
      'order' => 'DESC',
      ];

      if ($currentMarket) {
      $args['tax_query'] = [
      [
      'taxonomy' => 'analysis_market',
      'field' => 'slug',
      'terms' => $currentMarket,
      ]
      ];
      }

      $query = new WP_Query($args);
      @endphp

      @if ($query->have_posts())
      <div class="divide-y">
        @while ($query->have_posts())
        @php
        $query->the_post();

        /**
        * Ermittelt den fachlich relevanten Markt (Leaf-Term),
        * nicht den automatisch gesetzten Übermarkt
        */
        $terms = get_the_terms(get_the_ID(), 'analysis_market');
        $primaryMarket = null;

        if ($terms) {
        foreach ($terms as $term) {
        if ($term->parent !== 0) {
        $primaryMarket = $term;
        break;
        }
        }
        }
        @endphp

        <div class="grid grid-cols-[160px_140px_1fr_140px_160px] px-6 py-5 gap-4 items-start">

          {{-- Markt --}}
          <div class="font-medium">
            {{ $primaryMarket?->name }}
          </div>

          {{-- Preview --}}
          <div>
            @if ($image = get_field('chart_image'))
            <img
              src="{{ $image['sizes']['medium'] }}"
              class="w-full aspect-video object-cover rounded-md">
            @endif
          </div>

          {{-- Beschreibung --}}
          <p class="text-sm text-slate-600 leading-relaxed">
            {{ mb_strimwidth(strip_tags(get_field('content_text')), 0, 300, '…') }}
          </p>

          {{-- Datum --}}
          <div class="text-sm text-slate-500 whitespace-nowrap">
            {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
          </div>

          {{-- Aktion --}}
          <div>
            @if (can_view_analysis(get_current_user_id()))
            <a
              href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
              class="inline-flex items-center px-4 py-2 rounded-md bg-slate-100 hover:bg-slate-200 text-sm">
              View Report →
            </a>
            @else
            <a
              href="/dashboard/settings/billing"
              class="inline-flex items-center px-4 py-2 rounded-md bg-slate-200 text-slate-400 cursor-not-allowed text-sm">
              Payment required
            </a>
            @endif
          </div>

        </div>
        @endwhile
      </div>

      @php wp_reset_postdata(); @endphp
      @else
      <div class="px-6 py-6 text-slate-500">
        No analyses found.
      </div>
      @endif

    </div>

</section>
