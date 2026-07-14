<?php

function dashboard_redakteur_capabilities(): array
{
  return [
    'read' => true,
    'upload_files' => true,
    'edit_posts' => true,
    'edit_published_posts' => true,
    'publish_posts' => true,
    'edit_pages' => true,
    'edit_published_pages' => true,
    'publish_pages' => true,
    'dashboard_manage_content' => true,
  ];
}

function dashboard_user_can_manage_content(): bool
{
  return current_user_can('manage_options') || current_user_can('dashboard_manage_content') || current_user_can('edit_others_posts');
}

add_action('init', function () {
  $caps = dashboard_redakteur_capabilities();
  $role = get_role('redakteur');

  if (!$role) {
    add_role('redakteur', 'Redakteur', $caps);
    return;
  }

  foreach ($caps as $cap => $grant) {
    if ($grant) {
      $role->add_cap($cap);
    }
  }
});
