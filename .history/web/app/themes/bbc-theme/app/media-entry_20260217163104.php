<?php

add_action('init', function () {

  register_post_type('media_entry', [
    'labels' => [
      'name' => __('Media Entries'),
      'singular_name' => __('Media Entry'),
    ],
    'public' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'supports' => ['title'],
    'has_archive' => false,
    'rewrite' => false,
  ]);
});
