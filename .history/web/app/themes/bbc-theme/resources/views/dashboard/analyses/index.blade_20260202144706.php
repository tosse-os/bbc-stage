@php
$currentAsset = request()->get('asset');
$currentMarket = request()->get('market');
$dateFrom = request()->get('date_from');
$dateTo = request()->get('date_to');

$args = [
'post_type' => 'analysis',
'posts_per_page' => 20,
'orderby' => 'meta_value',
'meta_key' => 'publish_date',
'order' => 'DESC',
];

$taxQuery = [];

if ($currentAsset) {
$taxQuery[] = [
'taxonomy' => 'analysis_market',
'field' => 'slug',
'terms' => $currentAsset,
];
} elseif ($currentMarket) {
$taxQuery[] = [
'taxonomy' => 'analysis_market',
'field' => 'slug',
'terms' => $currentMarket,
];
}

if ($taxQuery) {
$args['tax_query'] = $taxQuery;
}

$metaQuery = [];

if ($dateFrom) {
$metaQuery[] = [
'key' => 'publish_date',
'value' => $dateFrom,
'compare' => '>=',
'type' => 'DATE',
];
}

if ($dateTo) {
$metaQuery[] = [
'key' => 'publish_date',
'value' => $dateTo,
'compare' => '<=', 'type'=> 'DATE',
  ];
  }

  if ($metaQuery) {
  $args['meta_query'] = $metaQuery;
  }

  $query = new WP_Query($args);
  @endphp

  {{-- FILTER --}}
  @include('dashboard.partials.filters')

  {{-- LISTE --}}
  <div class="mt-8 bg-white rounded-xl shadow-sm divide-y">

    @if ($query->have_posts())

    @while ($query->have_posts())
    @php
    $query->the_post();

    $terms = get_the_terms(get_the_ID(), 'analysis_market');
    $asset = null;

    if ($terms) {
    foreach ($terms as $term) {
    if ($term->parent !== 0) {
    $asset = $term;
    break;
    }
    }
    }
    @endphp

    <div class="grid grid-cols-[180px_1fr_140px_160px] gap-4 px-6 py-5 items-center">

      <div class="font-medium">
        {{ $asset?->name }}
      </div>

      <div class="text-sm text-slate-600">
        {{ mb_strimwidth(strip_tags(get_field('content_text')), 0, 160, '…') }}
      </div>

      <div class="text-sm text-slate-500">
        {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
      </div>

      <div class="text-right">
        <a
          href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
          class="inline-flex px-4 py-2 rounded-md bg-slate-100 hover:bg-slate-200 text-sm">
          View →
        </a>
      </div>

    </div>

    @endwhile

    @php wp_reset_postdata(); @endphp

    @else

    <div class="px-6 py-10 text-center text-slate-500">
      Keine Analysen gefunden.
    </div>

    @endif

  </div>
