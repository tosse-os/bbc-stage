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
