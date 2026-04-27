<?php

namespace App;

add_action('admin_post_export_landing', function () {

  if (!current_user_can('administrator')) {
    exit;
  }

  $post_id = (int) ($_GET['post_id'] ?? 0);
  if (!$post_id) exit;

  $data = [
    'hero_headline' => get_field('hero_headline', $post_id),
    'hero_subline' => get_field('hero_subline', $post_id),
    'hero_additional_text' => get_field('hero_additional_text', $post_id),

    'about' => [
      'headline' => get_field('about_section_headline', $post_id),
      'intro' => get_field('about_intro', $post_id),
      'features' => [
        get_field('feature_1', $post_id),
        get_field('feature_2', $post_id),
        get_field('feature_3', $post_id),
        get_field('feature_4', $post_id),
      ],
    ],

    'contact_text' => get_field('contact_text', $post_id),

    'market_insights' => [
      'headline' => get_field('market_insights_headline', $post_id),
      'subline' => get_field('market_insights_subline', $post_id),
      'additional' => get_field('market_insights_additional_text', $post_id),
      'items' => [
        get_field('market_insight_1_title', $post_id),
        get_field('market_insight_2_title', $post_id),
        get_field('market_insight_3_title', $post_id),
      ],
    ],
  ];

  header('Content-Type: application/json; charset=utf-8');
  header('Content-Disposition: attachment; filename=landing-export.json');

  echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

  exit;
});
