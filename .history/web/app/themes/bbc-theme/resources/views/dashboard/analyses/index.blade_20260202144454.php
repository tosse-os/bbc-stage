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

/**
* TAX QUERY
* Asset (Leaf) hat Vorrang vor Kategorie
*/
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

if (!empty($taxQuery)) {
$args['tax_query'] = $taxQuery;
}

/**
* DATE FILTER (ACF publish_date)
*/
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

  if (!empty($metaQuery)) {
  $args['meta_query'] = $metaQuery;
  }

  $query = new WP_Query($args);
  @endphp
