<?php

if (! defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'bbc_landing_translation_admin_menu');
add_action('admin_post_bbc_landing_translation_export', 'bbc_landing_translation_handle_export');
add_action('admin_post_bbc_landing_translation_import', 'bbc_landing_translation_handle_import');

function bbc_landing_translation_admin_menu(): void
{
    add_management_page(
        'Landing Translation',
        'Landing Translation',
        'manage_options',
        'bbc-landing-translation',
        'bbc_landing_translation_render_page'
    );
}

function bbc_landing_translation_render_page(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to access this page.', 'sage'));
    }

    $status = isset($_GET['bbc_landing_translation_status']) ? sanitize_key((string) wp_unslash($_GET['bbc_landing_translation_status'])) : '';
    $fields = isset($_GET['fields']) ? absint($_GET['fields']) : 0;
    $reviews = isset($_GET['reviews']) ? absint($_GET['reviews']) : 0;
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Landing Translation', 'sage'); ?></h1>

        <?php if ($status === 'imported') : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html(sprintf('%d Landingpage-Felder und %d Reviews wurden verarbeitet.', $fields, $reviews)); ?></p>
            </div>
        <?php elseif ($status === 'error') : ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html__('Der Import oder Export konnte nicht verarbeitet werden.', 'sage'); ?></p>
            </div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:24px;max-width:1200px;">
            <div class="card" style="max-width:none;">
                <h2><?php echo esc_html__('JSON exportieren', 'sage'); ?></h2>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="bbc_landing_translation_export">
                    <?php wp_nonce_field('bbc_landing_translation_export', 'bbc_landing_translation_nonce'); ?>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><label for="bbc_landing_translation_source_post_id"><?php echo esc_html__('Quellseite', 'sage'); ?></label></th>
                            <td><?php bbc_landing_translation_page_select('source_post_id', 'bbc_landing_translation_source_post_id'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="bbc_landing_translation_target_language"><?php echo esc_html__('Zielsprache', 'sage'); ?></label></th>
                            <td><?php bbc_landing_translation_language_field('target_language', 'bbc_landing_translation_target_language', 'en'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo esc_html__('Reviews', 'sage'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="include_reviews" value="1" checked>
                                    <?php echo esc_html__('Reviews mit exportieren', 'sage'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(__('Export herunterladen', 'sage')); ?>
                </form>
            </div>

            <div class="card" style="max-width:none;">
                <h2><?php echo esc_html__('JSON importieren', 'sage'); ?></h2>
                <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="bbc_landing_translation_import">
                    <?php wp_nonce_field('bbc_landing_translation_import', 'bbc_landing_translation_nonce'); ?>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><label for="bbc_landing_translation_target_post_id"><?php echo esc_html__('Zielseite', 'sage'); ?></label></th>
                            <td><?php bbc_landing_translation_page_select('target_post_id', 'bbc_landing_translation_target_post_id'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="bbc_landing_translation_import_file"><?php echo esc_html__('JSON-Datei', 'sage'); ?></label></th>
                            <td><input type="file" id="bbc_landing_translation_import_file" name="translation_file" accept="application/json,.json" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo esc_html__('Reviews', 'sage'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="import_reviews" value="1" checked>
                                    <?php echo esc_html__('Reviews mit importieren', 'sage'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(__('Import ausführen', 'sage'), 'primary', 'submit', true, ['onclick' => "return confirm('Import wirklich ausführen? Bestehende Zielwerte können überschrieben werden.');"]); ?>
                </form>
            </div>
        </div>
    </div>
    <?php
}

function bbc_landing_translation_page_select(string $name, string $id): void
{
    $pages = get_pages([
        'sort_column' => 'post_title',
        'sort_order' => 'ASC',
        'post_status' => ['publish', 'draft', 'private'],
    ]);

    echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" required>';
    echo '<option value="">' . esc_html__('Seite auswählen', 'sage') . '</option>';

    foreach ($pages as $page) {
        $language = bbc_landing_translation_post_language((int) $page->ID);
        $label = $page->post_title . ' #' . $page->ID;

        if ($language !== '') {
            $label .= ' [' . $language . ']';
        }

        echo '<option value="' . esc_attr((string) $page->ID) . '">' . esc_html($label) . '</option>';
    }

    echo '</select>';
}

function bbc_landing_translation_language_field(string $name, string $id, string $default = ''): void
{
    $languages = function_exists('pll_languages_list') ? pll_languages_list(['fields' => 'slug']) : [];

    if (is_array($languages) && count($languages) > 0) {
        echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" required>';

        foreach ($languages as $language) {
            $language = (string) $language;
            echo '<option value="' . esc_attr($language) . '"' . selected($language, $default, false) . '>' . esc_html($language) . '</option>';
        }

        echo '</select>';
        return;
    }

    echo '<input type="text" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" value="' . esc_attr($default) . '" required>';
}

function bbc_landing_translation_post_language(int $postId): string
{
    if (function_exists('pll_get_post_language')) {
        return (string) pll_get_post_language($postId, 'slug');
    }

    return '';
}

function bbc_landing_translation_handle_export(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to export translations.', 'sage'));
    }

    check_admin_referer('bbc_landing_translation_export', 'bbc_landing_translation_nonce');

    $sourcePostId = isset($_POST['source_post_id']) ? absint($_POST['source_post_id']) : 0;
    $targetLanguage = isset($_POST['target_language']) ? sanitize_key((string) wp_unslash($_POST['target_language'])) : '';
    $includeReviews = ! empty($_POST['include_reviews']);

    if (! bbc_landing_translation_is_page($sourcePostId) || $targetLanguage === '') {
        bbc_landing_translation_redirect_error();
    }

    $payload = bbc_landing_translation_build_export($sourcePostId, $targetLanguage, $includeReviews);
    $filename = sprintf('bbc-landing-translation-%d-%s-%s.json', $sourcePostId, $targetLanguage, gmdate('Ymd-His'));

    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function bbc_landing_translation_build_export(int $sourcePostId, string $targetLanguage, bool $includeReviews): array
{
    $sourceLanguage = bbc_landing_translation_post_language($sourcePostId);
    $targetPostId = bbc_landing_translation_get_translated_post_id($sourcePostId, $targetLanguage);
    $sections = [];

    foreach (bbc_landing_translation_field_map() as $sectionKey => $fields) {
        $sections[$sectionKey] = [];

        foreach ($fields as $fieldName => $config) {
            $value = function_exists('get_field') ? get_field($fieldName, $sourcePostId) : get_post_meta($sourcePostId, $fieldName, true);
            $sections[$sectionKey][$fieldName] = bbc_landing_translation_export_node($value, (string) ($config['type'] ?? 'text'), (bool) ($config['translatable'] ?? true));
        }
    }

    return [
        'schema' => 'bbc_landing_translation',
        'version' => 1,
        'created_at_utc' => gmdate('c'),
        'source' => [
            'post_id' => $sourcePostId,
            'language' => $sourceLanguage,
            'title' => get_the_title($sourcePostId),
            'slug' => get_post_field('post_name', $sourcePostId),
            'url' => get_permalink($sourcePostId),
        ],
        'target' => [
            'post_id' => $targetPostId ?: null,
            'language' => $targetLanguage,
        ],
        'sections' => $sections,
        'reviews' => $includeReviews ? bbc_landing_translation_export_reviews($sourceLanguage, $targetLanguage) : [],
    ];
}

function bbc_landing_translation_field_map(): array
{
    $marketInsightFields = [];

    foreach (range(1, 3) as $index) {
        $marketInsightFields["market_insight_{$index}_image"] = ['type' => 'media', 'translatable' => false];
        $marketInsightFields["market_insight_{$index}_title"] = ['type' => 'text'];
        $marketInsightFields["market_insight_{$index}_link"] = ['type' => 'url'];
        $marketInsightFields["market_insight_{$index}_duration"] = ['type' => 'text'];
        $marketInsightFields["market_insight_{$index}_platform"] = ['type' => 'key', 'translatable' => false];
        $marketInsightFields["market_insight_{$index}_premium"] = ['type' => 'bool', 'translatable' => false];
    }

    return [
        'hero' => [
            'hero_headline' => ['type' => 'html'],
            'hero_subline' => ['type' => 'html'],
            'hero_additional_text' => ['type' => 'html'],
            'hero_cta_text' => ['type' => 'text'],
            'hero_cta_link' => ['type' => 'link'],
            'hero_visual' => ['type' => 'media', 'translatable' => false],
        ],
        'market_insights' => array_merge([
            'market_insights_headline' => ['type' => 'html'],
            'market_insights_subline' => ['type' => 'html'],
            'market_insights_additional_text' => ['type' => 'html'],
        ], $marketInsightFields),
        'about' => [
            'about_section_headline' => ['type' => 'html'],
            'about_intro' => ['type' => 'html'],
            'about_visual' => ['type' => 'media', 'translatable' => false],
            'feature_1' => ['type' => 'mixed'],
            'feature_2' => ['type' => 'mixed'],
            'feature_3' => ['type' => 'mixed'],
            'feature_4' => ['type' => 'mixed'],
        ],
        'team' => [
            'team_headline' => ['type' => 'html'],
            'team_subheadline' => ['type' => 'html'],
            'team_bottomline' => ['type' => 'html'],
            'team_members' => ['type' => 'mixed'],
        ],
        'cta' => [
            'cta_headline_top' => ['type' => 'text'],
            'cta_headline_main' => ['type' => 'html'],
            'cta_subline' => ['type' => 'html'],
            'cta_button_text' => ['type' => 'text'],
            'cta_button_link' => ['type' => 'link'],
            'cta_note' => ['type' => 'text'],
        ],
        'contact' => [
            'contact_headline' => ['type' => 'html'],
            'contact_intro' => ['type' => 'html'],
            'contact_options' => ['type' => 'mixed'],
            'form_heading' => ['type' => 'text'],
            'email_placeholder' => ['type' => 'text'],
            'subject_placeholder' => ['type' => 'text'],
            'message_placeholder' => ['type' => 'text'],
            'submit_label' => ['type' => 'text'],
            'success_message' => ['type' => 'text'],
            'contact_form' => ['type' => 'mixed'],
            'contact_form_headline' => ['type' => 'text'],
            'contact_form_success' => ['type' => 'text'],
            'contact_form_email_placeholder' => ['type' => 'text'],
            'contact_form_message_placeholder' => ['type' => 'text'],
            'contact_form_button_text' => ['type' => 'text'],
        ],
    ];
}

function bbc_landing_translation_export_node($value, string $type, bool $translatable = true)
{
    if (is_array($value)) {
        if (bbc_landing_translation_is_media_array($value)) {
            return [
                'type' => 'media',
                'translatable' => false,
                'source' => $value,
            ];
        }

        $items = [];

        foreach ($value as $key => $item) {
            $items[$key] = bbc_landing_translation_export_node($item, bbc_landing_translation_nested_type((string) $key, $type), $translatable);
        }

        return [
            'type' => $type,
            'translatable' => $translatable,
            'items' => $items,
        ];
    }

    if (is_string($value)) {
        $node = [
            'type' => $type,
            'translatable' => $translatable,
            'source' => $value,
        ];

        if ($translatable) {
            $node['target'] = '';
        }

        return $node;
    }

    return [
        'type' => $type,
        'translatable' => false,
        'source' => $value,
    ];
}

function bbc_landing_translation_nested_type(string $key, string $fallback): string
{
    if ($key === 'url') {
        return 'url';
    }

    if (in_array($key, ['target', 'title', 'label', 'headline', 'name', 'position', 'company', 'button_text', 'duration', 'role'], true)) {
        return 'text';
    }

    if (in_array($key, ['text', 'intro', 'description', 'content', 'subline', 'bottomline'], true)) {
        return 'html';
    }

    if (in_array($key, ['image', 'visual', 'icon'], true)) {
        return 'media';
    }

    if ($fallback === 'link') {
        return 'text';
    }

    return $fallback;
}

function bbc_landing_translation_is_media_array(array $value): bool
{
    $keys = array_keys($value);

    return in_array('ID', $keys, true) && in_array('url', $keys, true) && (in_array('mime_type', $keys, true) || in_array('sizes', $keys, true));
}

function bbc_landing_translation_get_translated_post_id(int $postId, string $language): int
{
    if (function_exists('pll_get_post')) {
        return (int) pll_get_post($postId, $language);
    }

    return 0;
}

function bbc_landing_translation_export_reviews(string $sourceLanguage, string $targetLanguage): array
{
    if (! post_type_exists('review')) {
        return [];
    }

    $args = [
        'post_type' => 'review',
        'post_status' => ['publish', 'draft', 'private'],
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'suppress_filters' => false,
    ];

    if ($sourceLanguage !== '' && function_exists('pll_languages_list')) {
        $args['lang'] = $sourceLanguage;
    }

    $reviews = [];

    foreach (get_posts($args) as $post) {
        $postId = (int) $post->ID;
        $targetPostId = bbc_landing_translation_get_translated_post_id($postId, $targetLanguage);

        $reviews[] = [
            'source_post_id' => $postId,
            'target_post_id' => $targetPostId ?: null,
            'menu_order' => (int) $post->menu_order,
            'source_title' => get_the_title($postId),
            'fields' => [
                'reviewer_name' => bbc_landing_translation_export_node(bbc_landing_translation_get_field('reviewer_name', $postId, get_the_title($postId)), 'text'),
                'reviewer_position' => bbc_landing_translation_export_node(bbc_landing_translation_get_field('reviewer_position', $postId, ''), 'text'),
                'reviewer_company' => bbc_landing_translation_export_node(bbc_landing_translation_get_field('reviewer_company', $postId, ''), 'text'),
                'review_text' => bbc_landing_translation_export_node(bbc_landing_translation_get_field('review_text', $postId, ''), 'html'),
            ],
        ];
    }

    return $reviews;
}

function bbc_landing_translation_get_field(string $field, int $postId, $fallback = null)
{
    if (function_exists('get_field')) {
        $value = get_field($field, $postId);

        return $value !== null && $value !== '' ? $value : $fallback;
    }

    $value = get_post_meta($postId, $field, true);

    return $value !== '' ? $value : $fallback;
}

function bbc_landing_translation_handle_import(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('You are not allowed to import translations.', 'sage'));
    }

    check_admin_referer('bbc_landing_translation_import', 'bbc_landing_translation_nonce');

    $targetPostId = isset($_POST['target_post_id']) ? absint($_POST['target_post_id']) : 0;
    $importReviews = ! empty($_POST['import_reviews']);

    if (! bbc_landing_translation_is_page($targetPostId) || empty($_FILES['translation_file']['tmp_name'])) {
        bbc_landing_translation_redirect_error();
    }

    $file = $_FILES['translation_file'];

    if (! empty($file['error']) || ! is_uploaded_file($file['tmp_name']) || (int) ($file['size'] ?? 0) > 2097152) {
        bbc_landing_translation_redirect_error();
    }

    $raw = file_get_contents($file['tmp_name']);
    $payload = json_decode((string) $raw, true);

    if (! bbc_landing_translation_validate_payload($payload) || ! bbc_landing_translation_validate_target_post($targetPostId, $payload)) {
        bbc_landing_translation_redirect_error();
    }

    bbc_landing_translation_write_backup($targetPostId, $payload, $importReviews);

    $fieldCount = bbc_landing_translation_import_sections($targetPostId, $payload);
    $reviewCount = $importReviews ? bbc_landing_translation_import_reviews($payload) : 0;

    $redirect = add_query_arg([
        'page' => 'bbc-landing-translation',
        'bbc_landing_translation_status' => 'imported',
        'fields' => $fieldCount,
        'reviews' => $reviewCount,
    ], admin_url('tools.php'));

    wp_safe_redirect($redirect);
    exit;
}

function bbc_landing_translation_validate_payload($payload): bool
{
    if (! is_array($payload)) {
        return false;
    }

    if (($payload['schema'] ?? '') !== 'bbc_landing_translation' || (int) ($payload['version'] ?? 0) !== 1) {
        return false;
    }

    if (! isset($payload['target']['language']) || sanitize_key((string) $payload['target']['language']) === '') {
        return false;
    }

    return isset($payload['sections']) && is_array($payload['sections']);
}

function bbc_landing_translation_validate_target_post(int $targetPostId, array $payload): bool
{
    if (! bbc_landing_translation_is_page($targetPostId)) {
        return false;
    }

    $payloadLanguage = sanitize_key((string) ($payload['target']['language'] ?? ''));
    $postLanguage = bbc_landing_translation_post_language($targetPostId);

    if ($payloadLanguage !== '' && $postLanguage !== '' && $payloadLanguage !== $postLanguage) {
        return false;
    }

    return true;
}

function bbc_landing_translation_is_page(int $postId): bool
{
    return $postId > 0 && get_post_status($postId) !== false && get_post_type($postId) === 'page';
}

function bbc_landing_translation_import_sections(int $targetPostId, array $payload): int
{
    $count = 0;
    $sections = isset($payload['sections']) && is_array($payload['sections']) ? $payload['sections'] : [];

    foreach (bbc_landing_translation_field_map() as $sectionKey => $fields) {
        if (! isset($sections[$sectionKey]) || ! is_array($sections[$sectionKey])) {
            continue;
        }

        foreach ($fields as $fieldName => $config) {
            if (! array_key_exists($fieldName, $sections[$sectionKey])) {
                continue;
            }

            $value = bbc_landing_translation_import_node($sections[$sectionKey][$fieldName], (string) ($config['type'] ?? 'text'));
            bbc_landing_translation_update_field($fieldName, $value, $targetPostId);
            $count++;
        }
    }

    return $count;
}

function bbc_landing_translation_import_node($node, string $type)
{
    if (! is_array($node)) {
        return bbc_landing_translation_sanitize_value($node, $type);
    }

    if (array_key_exists('items', $node) && is_array($node['items'])) {
        $value = [];

        foreach ($node['items'] as $key => $item) {
            $value[$key] = bbc_landing_translation_import_node($item, bbc_landing_translation_nested_type((string) $key, $type));
        }

        return $value;
    }

    if (($node['translatable'] ?? false) && array_key_exists('target', $node)) {
        $target = $node['target'];
        $source = $node['source'] ?? '';
        $candidate = $target !== '' && $target !== null ? $target : $source;

        return bbc_landing_translation_sanitize_value($candidate, (string) ($node['type'] ?? $type));
    }

    if (array_key_exists('source', $node)) {
        return bbc_landing_translation_sanitize_value($node['source'], (string) ($node['type'] ?? $type));
    }

    return '';
}

function bbc_landing_translation_sanitize_value($value, string $type)
{
    if (is_array($value)) {
        $sanitized = [];

        foreach ($value as $key => $item) {
            $sanitized[$key] = bbc_landing_translation_sanitize_value($item, bbc_landing_translation_nested_type((string) $key, $type));
        }

        return $sanitized;
    }

    if ($type === 'bool') {
        return (bool) $value;
    }

    if (! is_string($value)) {
        return $value;
    }

    if ($type === 'html' || $type === 'mixed') {
        return wp_kses_post($value);
    }

    if ($type === 'url' || $type === 'link') {
        return esc_url_raw($value);
    }

    if ($type === 'key') {
        return sanitize_key($value);
    }

    return sanitize_text_field($value);
}

function bbc_landing_translation_update_field(string $fieldName, $value, int $postId): void
{
    if (function_exists('update_field')) {
        update_field($fieldName, $value, $postId);
        return;
    }

    update_post_meta($postId, $fieldName, $value);
}

function bbc_landing_translation_import_reviews(array $payload): int
{
    if (! post_type_exists('review')) {
        return 0;
    }

    $reviews = isset($payload['reviews']) && is_array($payload['reviews']) ? $payload['reviews'] : [];
    $targetLanguage = isset($payload['target']['language']) ? sanitize_key((string) $payload['target']['language']) : '';
    $count = 0;

    foreach ($reviews as $review) {
        if (! is_array($review)) {
            continue;
        }

        $sourcePostId = isset($review['source_post_id']) ? absint($review['source_post_id']) : 0;
        $targetPostId = isset($review['target_post_id']) ? absint($review['target_post_id']) : 0;

        if ($sourcePostId > 0 && $targetLanguage !== '') {
            $translatedId = bbc_landing_translation_get_translated_post_id($sourcePostId, $targetLanguage);

            if ($translatedId > 0) {
                $targetPostId = $translatedId;
            }
        }

        $fields = isset($review['fields']) && is_array($review['fields']) ? $review['fields'] : [];
        $name = isset($fields['reviewer_name']) ? bbc_landing_translation_import_node($fields['reviewer_name'], 'text') : (string) ($review['source_title'] ?? 'Review');

        if ($targetPostId <= 0 || get_post_status($targetPostId) === false) {
            $targetPostId = wp_insert_post([
                'post_type' => 'review',
                'post_status' => 'publish',
                'post_title' => $name !== '' ? $name : 'Review',
                'menu_order' => isset($review['menu_order']) ? (int) $review['menu_order'] : 0,
            ], true);

            if (is_wp_error($targetPostId)) {
                continue;
            }

            $targetPostId = (int) $targetPostId;

            if ($targetLanguage !== '' && function_exists('pll_set_post_language')) {
                pll_set_post_language($targetPostId, $targetLanguage);
            }

            if ($sourcePostId > 0 && function_exists('pll_save_post_translations') && function_exists('pll_get_post_language')) {
                $sourceLanguage = (string) pll_get_post_language($sourcePostId, 'slug');
                $translations = [];

                if ($sourceLanguage !== '') {
                    $translations[$sourceLanguage] = $sourcePostId;
                }

                if ($targetLanguage !== '') {
                    $translations[$targetLanguage] = $targetPostId;
                }

                if (count($translations) >= 2) {
                    pll_save_post_translations($translations);
                }
            }
        }

        wp_update_post([
            'ID' => $targetPostId,
            'post_title' => $name !== '' ? $name : get_the_title($targetPostId),
            'menu_order' => isset($review['menu_order']) ? (int) $review['menu_order'] : 0,
        ]);

        foreach (['reviewer_name' => 'text', 'reviewer_position' => 'text', 'reviewer_company' => 'text', 'review_text' => 'html'] as $fieldName => $type) {
            if (isset($fields[$fieldName])) {
                bbc_landing_translation_update_field($fieldName, bbc_landing_translation_import_node($fields[$fieldName], $type), $targetPostId);
            }
        }

        if ($sourcePostId > 0 && get_post_status($sourcePostId) !== false) {
            foreach (['review_rating', 'review_featured', 'review_image'] as $copyField) {
                $sourceValue = bbc_landing_translation_get_field($copyField, $sourcePostId, null);

                if ($sourceValue !== null && $sourceValue !== '') {
                    bbc_landing_translation_update_field($copyField, $sourceValue, $targetPostId);
                }
            }

            if (has_post_thumbnail($sourcePostId) && ! has_post_thumbnail($targetPostId)) {
                set_post_thumbnail($targetPostId, get_post_thumbnail_id($sourcePostId));
            }
        }

        $count++;
    }

    return $count;
}

function bbc_landing_translation_write_backup(int $targetPostId, array $payload, bool $includeReviews): void
{
    $upload = wp_upload_dir();

    if (! empty($upload['error']) || empty($upload['basedir'])) {
        return;
    }

    $dir = trailingslashit($upload['basedir']) . 'bbc-landing-translation-backups';

    if (! wp_mkdir_p($dir)) {
        return;
    }

    $targetLanguage = bbc_landing_translation_post_language($targetPostId);
    $backup = bbc_landing_translation_build_export($targetPostId, $targetLanguage !== '' ? $targetLanguage : 'backup', $includeReviews);
    $file = trailingslashit($dir) . sprintf('backup-%d-%s.json', $targetPostId, gmdate('Ymd-His'));

    file_put_contents($file, wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function bbc_landing_translation_redirect_error(): void
{
    wp_safe_redirect(add_query_arg([
        'page' => 'bbc-landing-translation',
        'bbc_landing_translation_status' => 'error',
    ], admin_url('tools.php')));
    exit;
}
