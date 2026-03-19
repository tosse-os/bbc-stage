add_action('init', function () {
register_post_type('analysis', [
'labels' => [
'name' => __('Analysen'),
'singular_name' => __('Analyse'),
],
'public' => false,
'show_ui' => true,
'show_in_menu' => true,
'supports' => ['title'],
'has_archive' => false,
'rewrite' => false,
]);
});
