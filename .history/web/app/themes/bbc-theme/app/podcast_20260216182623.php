<?php

add_action('init', function () {

  register_post_type('podcast', [
    'labels' => [
      'name' => __('Podcasts'),
      'singular_name' => __('Podcast'),
    ],
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'supports' => ['title'],
    'has_archive' => false,
    'rewrite' => [
      'slug' => 'podcast',
      'with_front' => false,
    ],
    'exclude_from_search' => true,
  ]);
});
