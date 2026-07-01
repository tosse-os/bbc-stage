<?php

/**
 * Theme setup.
 */

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 *
 * @return array
 */
// add_filter('block_editor_settings_all', function ($settings) {
//     $style = Vite::asset('resources/css/editor.css');

//     $settings['styles'][] = [
//         'css' => "@import url('{$style}')",
//     ];

//     return $settings;
// });


/**
 * Prüft, ob der aktuelle Admin-Screen die Theme-Editor-Assets benötigt.
 * Gilt für die Bearbeitung definierter Inhaltstypen im Backend.
 */
function should_load_admin_editor_assets(): bool
{
    if (!is_admin()) {
        return false;
    }

    $screen = get_current_screen();

    if (!$screen) {
        return false;
    }

    if (!in_array($screen->base, ['post', 'post-new'], true)) {
        return false;
    }

    return in_array($screen->post_type, ['page', 'media_entry', 'analysis'], true);
}

/**
 * Registriert die WordPress-Abhängigkeiten für das Backend-Editor-Script.
 * Nutzt die von Vite erzeugte Dependency-Liste für editor.js.
 */
function enqueue_admin_editor_script_dependencies(): void
{
    $dependencies = json_decode(Vite::content('editor.deps.json'), true);

    if (!is_array($dependencies)) {
        return;
    }

    foreach ($dependencies as $dependency) {
        if (!wp_script_is($dependency, 'enqueued')) {
            wp_enqueue_script($dependency);
        }
    }
}

/**
 * Lädt die Backend-Assets für die Bearbeitung rollenunabhängig.
 * CSS und JS werden auf denselben Edit-Screens für alle Rollen identisch eingebunden.
 */
add_action('admin_head', function () {
    if (!should_load_admin_editor_assets()) {
        return;
    }

    enqueue_admin_editor_script_dependencies();

    echo Vite::withEntryPoints([
        'resources/css/editor.css',
        'resources/js/editor.js',
    ])->toHtml();
}, 20);

/**
 * Use the generated theme.json file.
 *
 * @return string
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'footer_navigation' => __('Footer Navigation', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});

/**
 * Clean up internal links - remove domain - make relative
 */
add_filter('the_content', function ($content) {
    $home = preg_quote(home_url(), '/');

    return preg_replace(
        '/href=(["\'])' . $home . '(\/[^"\']*)?\1/i',
        'href="$2"',
        $content
    );
});


/**
 * Dashboard
 */
require_once __DIR__ . '/analysis.php';
require_once __DIR__ . '/analysis-market-admin.php';
require_once __DIR__ . '/analysis-market-import.php';
require_once __DIR__ . '/analysis-access.php';

require_once __DIR__ . '/dashboard-auth.php';
require_once __DIR__ . '/dashboard-avatar.php';
require_once __DIR__ . '/dashboard-access.php';
require_once __DIR__ . '/dashboard-secure-media.php';
require_once __DIR__ . '/stripe.php';
require_once __DIR__ . '/dashboard-gate.php';
require_once __DIR__ . '/dashboard-controller.php';

require_once __DIR__ . '/media-entry.php';
require_once __DIR__ . '/media-entry-access.php';

require_once __DIR__ . '/ajax.php';
require_once __DIR__ . '/filters.php';
require_once __DIR__ . '/translations.php';

require_once __DIR__ . '/helper/dashboard.php';
require_once __DIR__ . '/dashboard-admin.php';
require_once __DIR__ . '/dashboard-subscription.php';
require_once __DIR__ . '/stripe-checkout.php';
require_once __DIR__ . '/stripe-webhook.php';

require_once __DIR__ . '/acf-chart-image-validation.php';

#require_once __DIR__ . '/sql-query-admin.php';//only debug cases

require_once __DIR__ . '/export-landing.php';
##require_once __DIR__ . '/import-landing-admin.php';


/**
 * Dashboard - Permissions
 */

add_filter('show_admin_bar', function ($show) {
    if (!is_user_logged_in()) {
        return false;
    }

    return current_user_can('administrator');
});




/**
 * Harter Dashboard-Logout
 * - zerstört Session & Cookies
 * - verhindert Auto-Reauth
 * - Redirect auf Login-Seite
 */

add_action('init', function () {
    if (isset($_GET['dashboard_logout'])) {

        $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce'] ?? ''));

        if (!$nonce || !wp_verify_nonce($nonce, 'dashboard_logout')) {
            wp_safe_redirect('/dashboard-login?error=invalid_request');
            exit;
        }

        if (is_user_logged_in()) {
            wp_clear_auth_cookie();
            wp_logout();
        }

        // Cache & Session hart trennen
        nocache_headers();

        wp_safe_redirect('/dashboard-login?logged_out=1');
        exit;
    }
});



/* Admin Backend */


/**
 * Fügt Template + ID Spalten NUR für Admins hinzu
 */
add_filter('manage_pages_columns', function ($columns) {

    if (!current_user_can('administrator')) {
        return $columns;
    }

    $columns['page_template'] = 'Template';
    $columns['page_id'] = 'ID';

    return $columns;
});


/**
 * Befüllt Template + ID Spalten NUR für Admins
 */
add_action('manage_pages_custom_column', function ($column, $post_id) {

    if (!current_user_can('administrator')) {
        return;
    }

    if ($column === 'page_template') {

        $template = get_page_template_slug($post_id);

        if (!$template) {
            echo 'Standard';
            return;
        }

        $theme = wp_get_theme();
        $templates = $theme->get_page_templates();
        $label = array_search($template, $templates, true);

        echo $label ?: $template;
    }

    if ($column === 'page_id') {
        echo $post_id;
    }
}, 10, 2);


/**
 * Styling nur für Admins
 */
add_action('admin_enqueue_scripts', function () {

    // if (!current_user_can('administrator')) {
    //     return;
    // }

    $screen = get_current_screen();

    if (!$screen) return;

    if ($screen->post_type !== 'page') return;

    echo \Illuminate\Support\Facades\Vite::withEntryPoints([
        'resources/css/admin.css',
    ])->toHtml();

    wp_add_inline_style('wp-admin', '
        .wp-list-table .column-page_template {
            width: 220px;
            white-space: nowrap;
        }
        .wp-list-table .column-page_id {
            width: 80px;
            text-align: right;
            font-weight: 600;
        }
    ');
});


/* added for redakeur role */

add_action('add_meta_boxes', function () {

    global $post;

    if (!$post || get_post_type($post) !== 'page') return;

    $template = get_page_template_slug($post->ID);

    if (!in_array($template, [
        'page-landing.blade.php',
        'template-plain.blade.php'
    ])) return;

    remove_meta_box('pageparentdiv', 'page', 'side');
    remove_meta_box('authordiv', 'page', 'side');
    remove_meta_box('authordiv', 'page', 'normal');
    remove_meta_box('postimagediv', 'page', 'side');
    remove_meta_box('commentstatusdiv', 'page', 'normal');
    remove_meta_box('commentsdiv', 'page', 'normal');
}, 99);



add_filter('use_block_editor_for_post', function ($use, $post) {

    if (!$post || $post->post_type !== 'page') {
        return $use;
    }

    $template = get_page_template_slug($post->ID);

    if (in_array($template, [
        'page-landing.blade.php',
        'template-plain.blade.php'
    ])) {
        return false;
    }

    return $use;
}, 10, 2);



add_action('admin_head', function () {

    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'page') return;

    global $post;
    if (!$post) return;

    $template = get_page_template_slug($post->ID);

    if (!in_array($template, [
        'page-landing.blade.php',
        'template-plain.blade.php'
    ])) return;

    echo \Illuminate\Support\Facades\Vite::withEntryPoints([
        'resources/js/landingpage-admin.js',
    ])->toHtml();

    echo '<style>
        #postdivrich,
        #postdiv,
        #contentdiv {
            display: none !important;
        }
    </style>';
});

add_action('admin_init', function () {

    remove_post_type_support('page', 'author');
});


/* kann boxen nicht ausblenden */

add_filter('hidden_meta_boxes', function ($hidden, $screen) {
    if ($screen->id === 'page') {
        return [];
    }
    return $hidden;
}, 10, 2);


/* kann nicht verschieben */

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === "post.php" || $hook === "post-new.php") {
        $screen = get_current_screen();
        if ($screen->post_type === "page" && current_user_can("editor")) {
            wp_add_inline_script("jquery-ui-sortable", '
                jQuery(function($){
                    $(".meta-box-sortables").sortable("disable");
                });
            ');
        }
    }
});



/**
 * Blendet im Admin alle Seiten ohne Template aus (außer für Admins)
 */
add_action('pre_get_posts', function ($query) {

    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if (current_user_can('administrator')) {
        return;
    }

    global $pagenow;

    if ($pagenow !== 'edit.php') {
        return;
    }

    if ($query->get('post_type') !== 'page') {
        return;
    }

    $query->set('meta_query', [
        [
            'key'     => '_wp_page_template',
            'compare' => 'EXISTS',
        ],
        [
            'key'     => '_wp_page_template',
            'value'   => '',
            'compare' => '!=',
        ],
        [
            'key'     => '_wp_page_template',
            'value'   => 'default',
            'compare' => '!=',
        ],
    ]);
});



/**
 * Entfernt Beiträge und Kommentare aus dem Admin-Menü für Redakteure
 */
add_action('admin_menu', function () {

    if (current_user_can('administrator')) {
        return;
    }

    remove_menu_page('edit.php'); // Beiträge
    remove_menu_page('edit-comments.php'); // Kommentare

}, 999);



/* language workaround */

// add_filter('pll_the_language_link', function ($url, $slug, $locale) {
//     if (is_admin()) {
//         return $url;
//     }

//     if (!is_front_page()) {
//         return $url;
//     }

//     if ($url !== null) {
//         return $url;
//     }

//     return function_exists('pll_home_url') ? pll_home_url('de') : home_url('/');
// }, 10, 3);

if (file_exists(__DIR__ . '/reviews.php')) {
    require_once __DIR__ . '/reviews.php';
}
