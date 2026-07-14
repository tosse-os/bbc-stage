<?php

namespace App;

add_action('admin_post_import_landing', function () {

  if (!current_user_can('administrator')) {
    exit;
  }

  $post_id = (int) ($_GET['post_id'] ?? 0);
  if (!$post_id) exit;

  if (empty($_FILES['file']['tmp_name'])) {
    echo 'No file';
    exit;
  }

  $json = file_get_contents($_FILES['file']['tmp_name']);
  $data = json_decode($json, true);

  if (!$data) {
    echo 'Invalid JSON';
    exit;
  }

  update_field('hero_headline', $data['hero_headline'] ?? '', $post_id);
  // update_field('hero_subline', $data['hero_subline'] ?? '', $post_id);
  // update_field('hero_additional_text', $data['hero_additional_text'] ?? '', $post_id);

  // update_field('about_section_headline', $data['about']['headline'] ?? '', $post_id);
  // update_field('about_intro', $data['about']['intro'] ?? '', $post_id);

  // if (!empty($data['about']['features'])) {
  //   update_field('feature_1', $data['about']['features'][0] ?? '', $post_id);
  //   update_field('feature_2', $data['about']['features'][1] ?? '', $post_id);
  //   update_field('feature_3', $data['about']['features'][2] ?? '', $post_id);
  //   update_field('feature_4', $data['about']['features'][3] ?? '', $post_id);
  // }

  // update_field('contact_text', $data['contact_text'] ?? '', $post_id);

  // update_field('market_insights_headline', $data['market_insights']['headline'] ?? '', $post_id);
  // update_field('market_insights_subline', $data['market_insights']['subline'] ?? '', $post_id);
  // update_field('market_insights_additional_text', $data['market_insights']['additional'] ?? '', $post_id);

  // if (!empty($data['market_insights']['items'])) {
  //   update_field('market_insight_1_title', $data['market_insights']['items'][0] ?? '', $post_id);
  //   update_field('market_insight_2_title', $data['market_insights']['items'][1] ?? '', $post_id);
  //   update_field('market_insight_3_title', $data['market_insights']['items'][2] ?? '', $post_id);
  // }

  echo 'Import done';
  exit;
});
