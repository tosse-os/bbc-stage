<?php

/**
 * Custom Post Type: Analysis
 * Wird ausschließlich im Dashboard verwendet (nicht öffentlich).
 * Polylang-kompatibel, ACF-Felder werden angebunden.
 */

add_action('init', function () {
  register_post_type('analysis', [
    'labels' => [
      'name' => __('Analysen'),
      'singular_name' => __('Analyse'),
    ],
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'supports' => ['title'],
    'has_archive' => false,
    'rewrite' => [
      'slug' => 'analysis',
      'with_front' => false,
    ],
    'exclude_from_search' => true,
  ]);
});

/**
 * Märkte
 */

add_action('init', function () {

  register_taxonomy('analysis_market', ['analysis'], [
    'labels' => [
      'name' => __('Märkte'),
      'singular_name' => __('Markt'),
    ],
    'hierarchical' => true,
    'public' => false,
    'show_ui' => true,
    'show_admin_column' => true,
    'show_in_rest' => false,
    'rewrite' => false,
  ]);
});

/**
 * Checkboxverhalten bei den Kategorien
 */

add_action('set_object_terms', function ($object_id, $terms, $tt_ids, $taxonomy) {

  if ($taxonomy !== 'analysis_market') {
    return;
  }

  $all_terms = $terms;

  foreach ($terms as $term_id) {
    $term = get_term($term_id, $taxonomy);
    if ($term && $term->parent && !in_array($term->parent, $all_terms)) {
      $all_terms[] = $term->parent;
    }
  }

  wp_set_object_terms($object_id, $all_terms, $taxonomy, false);
}, 10, 4);
