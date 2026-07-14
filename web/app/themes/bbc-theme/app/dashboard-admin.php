<?php

if (!function_exists('bbc_editor_dashboard_user_can_view')) {
  function bbc_editor_dashboard_user_can_view(): bool
  {
    if (!is_admin() || !is_user_logged_in()) {
      return false;
    }

    if (current_user_can('manage_options') || current_user_can('edit_others_posts')) {
      return true;
    }

    $user = wp_get_current_user();
    $roles = (array) $user->roles;

    return (bool) array_intersect(['administrator', 'editor', 'redakteur'], $roles);
  }
}

add_action('wp_dashboard_setup', function () {
  if (!bbc_editor_dashboard_user_can_view()) {
    return;
  }

  remove_action('welcome_panel', 'wp_welcome_panel');

  $widgets = [
    'dashboard_quick_press',
    'dashboard_primary',
    'dashboard_site_health',
    'dashboard_right_now',
    'dashboard_activity',
    'dashboard_recent_comments',
    'dashboard_incoming_links',
    'dashboard_plugins',
    'dashboard_secondary',
    'dashboard_recent_drafts',
    'wp_activity_log_dashboard_widget',
    'wordfence_activity_report_widget',
  ];

  foreach ($widgets as $widget) {
    remove_meta_box($widget, 'dashboard', 'normal');
    remove_meta_box($widget, 'dashboard', 'side');
  }

  wp_add_dashboard_widget(
    'bbc_editor_dashboard_overview',
    'Redaktionsübersicht',
    'bbc_editor_dashboard_overview_render'
  );
}, 999);

add_filter('get_user_option_metaboxhidden_dashboard', function ($hidden) {
  if (!bbc_editor_dashboard_user_can_view()) {
    return $hidden;
  }

  $hidden = is_array($hidden) ? $hidden : [];

  return array_values(array_diff($hidden, ['bbc_editor_dashboard_overview']));
});

add_filter('get_user_option_screen_layout_dashboard', function ($value) {
  if (!bbc_editor_dashboard_user_can_view()) {
    return $value;
  }

  return 1;
});

add_action('admin_head-index.php', function () {
  if (!bbc_editor_dashboard_user_can_view()) {
    return;
  }

  echo '<style>
    #dashboard-widgets .postbox-container {
      width: 100% !important;
      float: none !important;
    }

    #dashboard-widgets #postbox-container-2,
    #dashboard-widgets #postbox-container-3,
    #dashboard-widgets #postbox-container-4 {
      display: none !important;
    }

    #bbc_editor_dashboard_overview .inside {
      margin: 0;
      padding: 0;
    }

    .bbc-editor-dashboard-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
      gap: 20px;
      padding: 16px;
    }

    .bbc-editor-dashboard-panel {
      min-width: 0;
      border: 1px solid #dcdcde;
      background: #fff;
    }

    .bbc-editor-dashboard-panel h3 {
      margin: 0;
      padding: 12px 14px;
      border-bottom: 1px solid #dcdcde;
      background: #f6f7f7;
      font-size: 14px;
    }

    .bbc-editor-dashboard-table-wrap {
      overflow-x: auto;
      width: 100%;
    }

    .bbc-editor-dashboard-table {
      width: 100%;
      min-width: 560px;
      border: 0;
    }

    .bbc-editor-dashboard-table th,
    .bbc-editor-dashboard-table td {
      vertical-align: top;
      line-height: 1.45;
    }

    .bbc-editor-dashboard-title {
      max-width: 260px;
      font-weight: 600;
    }

    .bbc-editor-dashboard-meta {
      color: #50575e;
    }

    .bbc-editor-dashboard-actions {
      width: 96px;
      text-align: right;
      white-space: nowrap;
    }

    .bbc-editor-dashboard-empty {
      padding: 14px;
      margin: 0;
      color: #646970;
    }

    @media screen and (max-width: 1300px) {
      .bbc-editor-dashboard-grid {
        grid-template-columns: 1fr;
      }

      .bbc-editor-dashboard-table {
        min-width: 680px;
      }
    }
  </style>';
});

function bbc_editor_dashboard_overview_render(): void
{
  echo '<div class="bbc-editor-dashboard-grid">';

  echo '<section class="bbc-editor-dashboard-panel">';
  echo '<h3>Letzte Analysen</h3>';
  bbc_editor_dashboard_render_table(
    bbc_editor_dashboard_get_posts('analysis'),
    'analysis'
  );
  echo '</section>';

  echo '<section class="bbc-editor-dashboard-panel">';
  echo '<h3>Letzte Media Entries</h3>';
  bbc_editor_dashboard_render_table(
    bbc_editor_dashboard_get_posts('media_entry'),
    'media_entry'
  );
  echo '</section>';

  echo '</div>';
}

function bbc_editor_dashboard_get_posts(string $post_type): array
{
  return get_posts([
    'post_type' => $post_type,
    'post_status' => ['publish', 'draft', 'pending', 'future', 'private'],
    'posts_per_page' => 20,
    'orderby' => 'date',
    'order' => 'DESC',
  ]);
}

function bbc_editor_dashboard_render_table(array $posts, string $type): void
{
  if (empty($posts)) {
    echo '<p class="bbc-editor-dashboard-empty">Keine Einträge gefunden.</p>';
    return;
  }

  echo '<div class="bbc-editor-dashboard-table-wrap">';
  echo '<table class="widefat striped bbc-editor-dashboard-table">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Titel</th>';

  if ($type === 'analysis') {
    echo '<th>Asset</th>';
    echo '<th>Datum</th>';
  }

  if ($type === 'media_entry') {
    echo '<th>Typ</th>';
    echo '<th>Datum</th>';
  }

  echo '<th>Status</th>';
  echo '<th></th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';

  foreach ($posts as $post) {
    $post_id = (int) $post->ID;
    $title = get_the_title($post_id);
    $title = $title !== '' ? $title : '(Ohne Titel)';
    $edit_link = get_edit_post_link($post_id);
    $status_object = get_post_status_object($post->post_status);
    $status_label = $status_object ? $status_object->label : $post->post_status;

    echo '<tr>';
    echo '<td class="bbc-editor-dashboard-title">' . esc_html($title) . '</td>';

    if ($type === 'analysis') {
      echo '<td class="bbc-editor-dashboard-meta">' . esc_html(bbc_editor_dashboard_get_analysis_asset($post_id) ?: '—') . '</td>';
      echo '<td class="bbc-editor-dashboard-meta">' . esc_html(bbc_editor_dashboard_get_analysis_date($post_id)) . '</td>';
    }

    if ($type === 'media_entry') {
      $media_type = get_post_meta($post_id, 'media_type', true);

      echo '<td class="bbc-editor-dashboard-meta">' . esc_html($media_type ?: '—') . '</td>';
      echo '<td class="bbc-editor-dashboard-meta">' . esc_html(get_the_date('d.m.Y', $post_id)) . '</td>';
    }

    echo '<td class="bbc-editor-dashboard-meta">' . esc_html($status_label) . '</td>';

    if ($edit_link && current_user_can('edit_post', $post_id)) {
      echo '<td class="bbc-editor-dashboard-actions"><a class="button button-small" href="' . esc_url($edit_link) . '">Bearbeiten</a></td>';
    } else {
      echo '<td class="bbc-editor-dashboard-actions"></td>';
    }

    echo '</tr>';
  }

  echo '</tbody>';
  echo '</table>';
  echo '</div>';
}

function bbc_editor_dashboard_get_analysis_asset(int $post_id): string
{
  $asset = (string) get_post_meta($post_id, 'report_asset_name', true);

  if ($asset !== '') {
    return $asset;
  }

  $terms = get_the_terms($post_id, 'analysis_market');

  if (empty($terms) || is_wp_error($terms)) {
    return '';
  }

  foreach ($terms as $term) {
    if ((int) $term->parent !== 0) {
      return html_entity_decode($term->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
  }

  return html_entity_decode($terms[0]->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function bbc_editor_dashboard_get_analysis_date(int $post_id): string
{
  $publish_date = (string) get_post_meta($post_id, 'publish_date', true);

  if ($publish_date !== '') {
    $timestamp = strtotime($publish_date);

    if ($timestamp) {
      return date_i18n('d.m.Y', $timestamp);
    }
  }

  return get_the_date('d.m.Y', $post_id);
}
