<?php

namespace App;

add_action('init', function () {
    $labels = [
        'name' => __('Reviews', 'sage'),
        'singular_name' => __('Review', 'sage'),
        'add_new_item' => __('Add New Review', 'sage'),
        'edit_item' => __('Edit Review', 'sage'),
        'new_item' => __('New Review', 'sage'),
        'view_item' => __('View Review', 'sage'),
        'search_items' => __('Search Reviews', 'sage'),
        'not_found' => __('No Reviews found', 'sage'),
        'not_found_in_trash' => __('No Reviews found in Trash', 'sage'),
        'all_items' => __('All Reviews', 'sage'),
        'menu_name' => __('Reviews', 'sage'),
    ];

    register_post_type('review', [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'has_archive' => false,
        'rewrite' => false,
        'query_var' => false,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => ['title', 'thumbnail', 'page-attributes'],
        'hierarchical' => false,
        'capability_type' => 'post',
    ]);
}, 9);

add_filter('pll_get_post_types', function ($postTypes, $isSettings) {
    $postTypes['review'] = 'review';

    return $postTypes;
}, 10, 2);

add_action('acf/init', function () {
    if (! function_exists('acf_add_options_sub_page') || ! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title' => __('Review Slider Settings', 'sage'),
        'menu_title' => __('Slider Settings', 'sage'),
        'menu_slug' => 'review-slider-settings',
        'parent_slug' => 'edit.php?post_type=review',
        'capability' => 'edit_posts',
        'redirect' => false,
        'post_id' => 'review_slider_settings',
    ]);

    acf_add_local_field_group([
        'key' => 'group_landing_reviews',
        'title' => __('Review Fields', 'sage'),
        'fields' => [
            [
                'key' => 'field_review_reviewer_name',
                'label' => __('Reviewer Name', 'sage'),
                'name' => 'reviewer_name',
                'type' => 'text',
            ],
            [
                'key' => 'field_review_reviewer_position',
                'label' => __('Reviewer Position', 'sage'),
                'name' => 'reviewer_position',
                'type' => 'text',
            ],
            [
                'key' => 'field_review_reviewer_company',
                'label' => __('Reviewer Company', 'sage'),
                'name' => 'reviewer_company',
                'type' => 'text',
            ],
            [
                'key' => 'field_review_review_text',
                'label' => __('Review Text', 'sage'),
                'name' => 'review_text',
                'type' => 'textarea',
                'rows' => 5,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_review_review_rating',
                'label' => __('Review Rating', 'sage'),
                'name' => 'review_rating',
                'type' => 'number',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default_value' => 5,
            ],
            [
                'key' => 'field_review_review_image',
                'label' => __('Review Image', 'sage'),
                'name' => 'review_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'all',
            ],
            [
                'key' => 'field_review_review_featured',
                'label' => __('Featured Review', 'sage'),
                'name' => 'review_featured',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'review',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_review_slider_settings',
        'title' => __('Review Slider Settings', 'sage'),
        'fields' => [
            [
                'key' => 'field_review_slider_autoplay',
                'label' => __('Autoplay', 'sage'),
                'name' => 'review_slider_autoplay',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_review_slider_speed',
                'label' => __('Autoplay Speed', 'sage'),
                'name' => 'review_slider_speed',
                'type' => 'number',
                'append' => 'ms',
                'min' => 1000,
                'step' => 100,
                'default_value' => 5000,
            ],
            [
                'key' => 'field_review_slider_per_step',
                'label' => __('Slides Per Step', 'sage'),
                'name' => 'review_slider_per_step',
                'type' => 'number',
                'min' => 1,
                'max' => 3,
                'step' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_review_slider_equal_height',
                'label' => __('Equal Card Height', 'sage'),
                'name' => 'review_slider_equal_height',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ],
            [
                'key' => 'field_review_slider_equal_text_length',
                'label' => __('Equal Text Length', 'sage'),
                'name' => 'review_slider_equal_text_length',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_review_slider_text_limit',
                'label' => __('Review Text Length', 'sage'),
                'name' => 'review_slider_text_limit',
                'type' => 'number',
                'append' => 'Zeichen',
                'min' => 80,
                'step' => 10,
                'default_value' => 260,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_review_slider_equal_text_length',
                            'operator' => '==',
                            'value' => '1',
                        ],
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'review-slider-settings',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);
});

add_action('pre_get_posts', function ($query) {
    if (! is_admin() || ! $query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'review') {
        return;
    }

    if ($query->get('orderby')) {
        return;
    }

    $query->set('orderby', 'menu_order title');
    $query->set('order', 'ASC');
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'edit.php') {
        return;
    }

    $screen = get_current_screen();

    if (! $screen || $screen->post_type !== 'review') {
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');
    wp_register_style('review-admin-order', false);
    wp_enqueue_style('review-admin-order');
    wp_add_inline_style('review-admin-order', '.post-type-review #the-list tr{cursor:move}.post-type-review #the-list tr.ui-sortable-helper{background:#fff;box-shadow:0 12px 34px rgba(0,0,0,.16)}');

    $nonce = wp_create_nonce('review_menu_order');
    $script = "jQuery(function($){var list=$('#the-list');if(!list.length){return;}list.sortable({items:'tr',axis:'y',cursor:'move',helper:function(e,ui){ui.children().each(function(){var cell=$(this);cell.width(cell.width());});return ui;},update:function(){var order=list.children('tr').map(function(){return this.id.replace('post-','');}).get();$.post(ajaxurl,{action:'review_update_menu_order',nonce:" . wp_json_encode($nonce) . ",order:order});}});});";
    wp_add_inline_script('jquery-ui-sortable', $script, 'after');
});

add_action('wp_ajax_review_update_menu_order', function () {
    check_ajax_referer('review_menu_order', 'nonce');

    if (! current_user_can('edit_posts')) {
        wp_send_json_error(null, 403);
    }

    $order = isset($_POST['order']) && is_array($_POST['order']) ? array_map('absint', $_POST['order']) : [];

    foreach ($order as $position => $postId) {
        if (get_post_type($postId) !== 'review' || ! current_user_can('edit_post', $postId)) {
            continue;
        }

        wp_update_post([
            'ID' => $postId,
            'menu_order' => $position,
        ]);
    }

    wp_send_json_success();
});
