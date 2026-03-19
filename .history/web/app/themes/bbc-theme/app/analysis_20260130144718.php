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

add_action('save_post_analysis', function ($post_id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  $terms = wp_get_post_terms($post_id, 'analysis_market', ['fields' => 'ids']);

  if (empty($terms)) {
    return;
  }

  $all_terms = $terms;

  foreach ($terms as $term_id) {
    $parent = get_term($term_id, 'analysis_market')->parent;
    if ($parent && !in_array($parent, $all_terms)) {
      $all_terms[] = $parent;
    }
  }

  wp_set_post_terms($post_id, $all_terms, 'analysis_market', false);
});
