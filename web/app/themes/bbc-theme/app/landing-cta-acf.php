<?php

if (! defined('ABSPATH')) {
    exit;
}

function bbc_landing_cta_acf_group(): array
{
    return array (
  'key' => 'group_bbc_landing_cta',
  'title' => 'Landing Page – 5. CTA Section',
  'fields' => 
  array (
    0 => 
    array (
      'key' => 'field_bbc_cta_tab_content',
      'label' => 'Content',
      'name' => '',
      'aria-label' => '',
      'type' => 'tab',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'placement' => 'top',
      'endpoint' => 0,
    ),
    1 => 
    array (
      'key' => 'field_bbc_cta_headline_top',
      'label' => 'Headline oben',
      'name' => 'cta_headline_top',
      'aria-label' => '',
      'type' => 'text',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'default_value' => 'Handeln Sie nicht schneller.',
      'maxlength' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
    ),
    2 => 
    array (
      'key' => 'field_bbc_cta_headline_main',
      'label' => 'Headline Haupttext',
      'name' => 'cta_headline_main',
      'aria-label' => '',
      'type' => 'text',
      'instructions' => 'HTML wie bei der Hero-Überschrift möglich, z. B. &lt;br&gt; und &lt;span class=&quot;text-brand-primary&quot;&gt;Text&lt;/span&gt;.',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'default_value' => '<span class="drop-shadow-[0_2px_4px_rgba(0,0,0,0.18)]">Handeln Sie</span> <span class="text-brand-primary">strukturierter.</span>',
      'maxlength' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
    ),
    3 => 
    array (
      'key' => 'field_bbc_cta_subline',
      'label' => 'Subline',
      'name' => 'cta_subline',
      'aria-label' => '',
      'type' => 'textarea',
      'instructions' => 'Erlaubt sind Zeilenumbrüche, &lt;br&gt;, &lt;strong&gt;, &lt;em&gt; und &lt;span&gt;.',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'default_value' => 'Märkte werden sich weiter bewegen.
Die Frage ist nicht ob, sondern <strong>wie vorbereitet</strong> Sie sind.',
      'maxlength' => '',
      'rows' => 3,
      'placeholder' => '',
      'new_lines' => '',
    ),
    4 => 
    array (
      'key' => 'field_bbc_cta_tab_cta',
      'label' => 'CTA',
      'name' => '',
      'aria-label' => '',
      'type' => 'tab',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'placement' => 'top',
      'endpoint' => 0,
    ),
    5 => 
    array (
      'key' => 'field_bbc_cta_button_text',
      'label' => 'Button Text',
      'name' => 'cta_button_text',
      'aria-label' => '',
      'type' => 'text',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '50',
        'class' => '',
        'id' => '',
      ),
      'default_value' => 'Kostenlose Analysen sichern',
      'maxlength' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
    ),
    6 => 
    array (
      'key' => 'field_bbc_cta_button_link',
      'label' => 'Button Link',
      'name' => 'cta_button_link',
      'aria-label' => '',
      'type' => 'link',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '50',
        'class' => '',
        'id' => '',
      ),
      'return_format' => 'array',
    ),
    7 => 
    array (
      'key' => 'field_bbc_cta_note',
      'label' => 'Hinweis unter Button',
      'name' => 'cta_note',
      'aria-label' => '',
      'type' => 'text',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'default_value' => 'Kein Risiko · 7 Tage kostenlos · jederzeit kündbar',
      'maxlength' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
    ),
  ),
  'location' => 
  array (
    0 => 
    array (
      0 => 
      array (
        'param' => 'page_template',
        'operator' => '==',
        'value' => 'views/page-landing.blade.php',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        'param' => 'post',
        'operator' => '==',
        'value' => '7',
      ),
    ),
    2 => 
    array (
      0 => 
      array (
        'param' => 'post',
        'operator' => '==',
        'value' => '511',
      ),
    ),
  ),
  'menu_order' => 50,
  'position' => 'normal',
  'style' => 'default',
  'label_placement' => 'top',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => true,
  'description' => '',
  'show_in_rest' => 0,
  'modified' => 1790000007,
);
}

function bbc_landing_cta_acf_find_group_ids(string $key): array
{
    $posts = get_posts([
        'post_type' => 'acf-field-group',
        'post_status' => ['publish', 'acf-disabled'],
        'name' => $key,
        'posts_per_page' => -1,
        'fields' => 'ids',
        'orderby' => 'ID',
        'order' => 'ASC',
        'suppress_filters' => true,
    ]);

    return array_map('intval', $posts ?: []);
}

function bbc_landing_cta_acf_delete_group_with_fields(int $group_id): void
{
    $children = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'post_parent' => $group_id,
        'posts_per_page' => -1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    foreach ($children as $child_id) {
        wp_delete_post((int) $child_id, true);
    }

    wp_delete_post($group_id, true);
}

function bbc_landing_cta_acf_find_group_id(string $key): int
{
    $group_ids = bbc_landing_cta_acf_find_group_ids($key);

    if (! $group_ids) {
        return 0;
    }

    $keep_id = (int) array_shift($group_ids);

    foreach ($group_ids as $duplicate_id) {
        bbc_landing_cta_acf_delete_group_with_fields((int) $duplicate_id);
    }

    return $keep_id;
}

function bbc_landing_cta_acf_find_field_id(string $key, int $group_id): int
{
    $posts = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'name' => $key,
        'post_parent' => $group_id,
        'posts_per_page' => 1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    if ($posts) {
        return (int) $posts[0];
    }

    $posts = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'name' => $key,
        'posts_per_page' => 1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    return $posts ? (int) $posts[0] : 0;
}

function bbc_landing_cta_acf_insert_or_update_group(array $group): int
{
    $fields = $group['fields'];
    unset($group['fields']);

    $group_id = bbc_landing_cta_acf_find_group_id((string) $group['key']);

    $post = [
        'post_type' => 'acf-field-group',
        'post_status' => $group['active'] ? 'publish' : 'acf-disabled',
        'post_title' => (string) $group['title'],
        'post_name' => (string) $group['key'],
        'post_excerpt' => '',
        'post_content' => maybe_serialize($group),
        'menu_order' => (int) $group['menu_order'],
    ];

    if ($group_id > 0) {
        $post['ID'] = $group_id;
        wp_update_post(wp_slash($post));
    } else {
        $group_id = (int) wp_insert_post(wp_slash($post));
    }

    if ($group_id > 0) {
        bbc_landing_cta_acf_insert_or_update_fields($group_id, $fields);
    }

    return $group_id;
}

function bbc_landing_cta_acf_insert_or_update_fields(int $group_id, array $fields): void
{
    $wanted_keys = [];

    foreach (array_values($fields) as $index => $field) {
        $field['parent'] = $group_id;
        $field['menu_order'] = $index;
        $wanted_keys[] = (string) $field['key'];

        $field_id = bbc_landing_cta_acf_find_field_id((string) $field['key'], $group_id);

        $post = [
            'post_type' => 'acf-field',
            'post_status' => 'publish',
            'post_parent' => $group_id,
            'post_title' => (string) $field['label'],
            'post_name' => (string) $field['key'],
            'post_excerpt' => (string) $field['name'],
            'post_content' => maybe_serialize($field),
            'menu_order' => $index,
        ];

        if ($field_id > 0) {
            $post['ID'] = $field_id;
            wp_update_post(wp_slash($post));
        } else {
            wp_insert_post(wp_slash($post));
        }
    }

    $children = get_posts([
        'post_type' => 'acf-field',
        'post_status' => ['publish', 'acf-disabled'],
        'post_parent' => $group_id,
        'posts_per_page' => -1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    foreach ($children as $child_id) {
        $child = get_post((int) $child_id);

        if ($child && ! in_array($child->post_name, $wanted_keys, true)) {
            wp_delete_post((int) $child_id, true);
        }
    }
}

function bbc_landing_cta_acf_install(): void
{
    if (! is_admin() || ! current_user_can('manage_options')) {
        return;
    }

    if (! function_exists('acf')) {
        return;
    }

    $version = '1.7';

    if (get_option('bbc_landing_cta_acf_db_version') === $version) {
        return;
    }

    bbc_landing_cta_acf_insert_or_update_group(bbc_landing_cta_acf_group());

    update_option('bbc_landing_cta_acf_db_version', $version, false);
}

add_action('admin_init', 'bbc_landing_cta_acf_install', 20);
