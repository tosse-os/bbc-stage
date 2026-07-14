<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PageLanding extends Composer
{
    protected static $views = [
        'page-landing'
    ];

    public function with()
    {
        $subscribeTrialUrl = $this->subscribeTrialUrl();

        return [
            'hero' => [
                'headline' => get_field('hero_headline'),
                'subline' => get_field('hero_subline'),
                'additional_text' => get_field('hero_additional_text'),
                'cta_text' => get_field('hero_cta_text'),
                'cta_link' => get_field('hero_cta_link'),
                'visual' => get_field('hero_visual')
            ],

            'about' => [
                'headline' => get_field('about_section_headline'),
                'intro' => get_field('about_intro'),
                'visual' => get_field('about_visual'),
                'features' => array_map(function ($i) {
                    return get_field("feature_{$i}");
                }, range(1, 4))
            ],

            'contact' => [
                'headline' => get_field('contact_headline'),
                'intro' => get_field('contact_intro'),
                'options' => get_field('contact_options') ?: [],
                'form' => $this->contactForm(),
            ],

            'team' => [
                'headline' => get_field('team_headline'),
                'subheadline' => get_field('team_subheadline'),
                'bottomline' => get_field('team_bottomline'),
                'members' => get_field('team_members') ?: [],
            ],

            'marketInsights' => [
                'headline' => get_field('market_insights_headline'),
                'subline' => get_field('market_insights_subline'),
                'additional' => get_field('market_insights_additional_text'),
                'items' => array_map(function ($i) {
                    return [
                        'image' => get_field("market_insight_{$i}_image"),
                        'title' => get_field("market_insight_{$i}_title"),
                        'link' => get_field("market_insight_{$i}_link"),
                        'duration' => get_field("market_insight_{$i}_duration"),
                        'platform' => get_field("market_insight_{$i}_platform"),
                        'premium' => get_field("market_insight_{$i}_premium"),
                    ];
                }, range(1, 3))
            ],
            'cta' => [
                'headline_top' => get_field('cta_headline_top'),
                'headline_main' => get_field('cta_headline_main'),
                'subline' => get_field('cta_subline'),
                'button_text' => get_field('cta_button_text'),
                'button_link' => get_field('cta_button_link'),
                'note' => get_field('cta_note'),
            ],

            'reviews' => $this->reviews(),
            'subscribeTrialUrl' => $subscribeTrialUrl,
        ];
    }


    private function subscribeTrialUrl(): string
    {
        $currentLanguage = function_exists('pll_current_language')
            ? (string) pll_current_language('slug')
            : '';

        foreach (['subscribe-trial', 'trial'] as $slug) {
            $page = get_page_by_path($slug);

            if (! $page) {
                continue;
            }

            $pageId = (int) $page->ID;

            if ($currentLanguage !== '' && function_exists('pll_get_post')) {
                $translatedPageId = pll_get_post($pageId, $currentLanguage);

                if ($translatedPageId) {
                    $pageId = (int) $translatedPageId;
                }
            }

            $url = get_permalink($pageId);

            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        return home_url('/subscribe-trial/');
    }


    private function contactForm(): array
    {
        $fields = $this->allFields();
        $group = $this->fieldValue('contact_form', []);

        if (! is_array($group)) {
            $group = $this->arrayValueDeep($fields, [
                'contact_form',
                'contact_form_fields',
                'contact_form_settings',
                'form',
                'form_fields',
                'form_settings',
            ], []);
        }

        if (! is_array($group)) {
            $group = [];
        }

        return [
            'headline' => $this->formValue([
                'contact_form_headline',
                'contact_form_title',
                'contact_form_heading',
                'form_headline',
                'form_title',
                'form_heading',
            ], ['headline', 'title', 'heading', 'form_heading'], $group, $fields),
            'success' => $this->formValue([
                'contact_form_success',
                'contact_form_success_message',
                'form_success',
                'form_success_message',
                'success_message',
            ], ['success', 'success_message'], $group, $fields),
            'email_placeholder' => $this->formValue([
                'contact_form_email_placeholder',
                'contact_form_email',
                'form_email_placeholder',
                'form_email',
                'email_placeholder',
            ], ['email_placeholder', 'email'], $group, $fields),
            'subject_placeholder' => $this->formValue([
                'contact_form_subject_placeholder',
                'form_subject_placeholder',
                'subject_placeholder',
            ], ['subject_placeholder', 'subject'], $group, $fields),
            'message_placeholder' => $this->formValue([
                'contact_form_message_placeholder',
                'contact_form_message',
                'form_message_placeholder',
                'form_message',
                'message_placeholder',
            ], ['message_placeholder', 'message'], $group, $fields),
            'button_text' => $this->formValue([
                'contact_form_button_text',
                'contact_form_button_label',
                'contact_form_submit_text',
                'contact_form_submit_label',
                'form_button_text',
                'form_button_label',
                'form_submit_text',
                'form_submit_label',
                'submit_label',
            ], ['button_text', 'button_label', 'submit_text', 'submit_label'], $group, $fields),
        ];
    }

    private function formValue(array $fieldNames, array $groupKeys, array $group, array $fields)
    {
        $value = $this->fieldValue($fieldNames, null);

        if ($value !== null && $value !== '') {
            return $value;
        }

        $value = $this->arrayValueDeep($group, array_merge($fieldNames, $groupKeys), null);

        if ($value !== null && $value !== '') {
            return $value;
        }

        return $this->arrayValueDeep($fields, $fieldNames, '');
    }

    private function allFields(): array
    {
        if (! function_exists('get_fields')) {
            return [];
        }

        $postId = function_exists('get_the_ID') ? (int) get_the_ID() : 0;
        $fields = $postId > 0 ? get_fields($postId) : get_fields();

        return is_array($fields) ? $fields : [];
    }

    private function fieldValue($fieldNames, $fallback = '')
    {
        $fieldNames = is_array($fieldNames) ? $fieldNames : [$fieldNames];

        foreach ($fieldNames as $fieldName) {
            if (! is_string($fieldName) || $fieldName === '' || ! function_exists('get_field')) {
                continue;
            }

            $value = get_field($fieldName);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    private function arrayValue(array $source, array $keys, $fallback = '')
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $source) && $source[$key] !== null && $source[$key] !== '') {
                return $source[$key];
            }
        }

        return $fallback;
    }

    private function arrayValueDeep(array $source, array $keys, $fallback = '')
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $source) && $source[$key] !== null && $source[$key] !== '') {
                return $source[$key];
            }
        }

        foreach ($source as $value) {
            if (! is_array($value)) {
                continue;
            }

            $match = $this->arrayValueDeep($value, $keys, null);

            if ($match !== null && $match !== '') {
                return $match;
            }
        }

        return $fallback;
    }

    private function reviews(): array
    {
        $args = [
            'post_type' => 'review',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'suppress_filters' => false,
        ];

        if (function_exists('pll_current_language')) {
            $args['lang'] = pll_current_language('slug');
        }

        $items = array_map(function ($post) {
            $postId = (int) $post->ID;
            $name = (string) $this->reviewField('reviewer_name', $postId, get_the_title($postId));
            $image = $this->reviewImage($this->reviewField('review_image', $postId), $postId);

            if (! $image['alt']) {
                $image['alt'] = $name;
            }

            return [
                'id' => $postId,
                'name' => $name,
                'position' => (string) $this->reviewField('reviewer_position', $postId, ''),
                'company' => (string) $this->reviewField('reviewer_company', $postId, ''),
                'text' => (string) $this->reviewField('review_text', $postId, ''),
                'rating' => max(1, min(5, (int) $this->reviewField('review_rating', $postId, 5))),
                'image' => $image,
                'featured' => (bool) $this->reviewField('review_featured', $postId, false),
            ];
        }, get_posts($args));

        return [
            'items' => $items,
            'settings' => $this->reviewSliderSettings(),
            'strings' => [
                'eyebrow' => $this->reviewTranslate('Customer Reviews'),
                'headline' => $this->reviewTranslate('Voices of Innovation'),
                'previous' => $this->reviewTranslate('Previous review'),
                'next' => $this->reviewTranslate('Next review'),
                'read_more' => $this->reviewTranslate('Read more'),
                'show_less' => $this->reviewTranslate('Show less'),
            ],
        ];
    }

    private function reviewSliderSettings(): array
    {
        $postId = 'review_slider_settings';
        $autoplay = function_exists('get_field') ? get_field('review_slider_autoplay', $postId) : true;
        $speed = function_exists('get_field') ? get_field('review_slider_speed', $postId) : 5000;
        $perStep = function_exists('get_field') ? get_field('review_slider_per_step', $postId) : 1;
        $equalHeight = function_exists('get_field') ? get_field('review_slider_equal_height', $postId) : false;
        $equalTextLength = function_exists('get_field') ? get_field('review_slider_equal_text_length', $postId) : true;
        $textLimit = function_exists('get_field') ? get_field('review_slider_text_limit', $postId) : 260;
        $textLimitValue = $textLimit === null || $textLimit === '' ? 260 : (int) $textLimit;

        return [
            'autoplay' => $autoplay === null ? true : (bool) $autoplay,
            'speed' => max(1000, (int) ($speed ?: 5000)),
            'per_step' => max(1, min(3, (int) ($perStep ?: 1))),
            'equal_height' => (bool) $equalHeight,
            'equal_text_length' => $equalTextLength === null ? true : (bool) $equalTextLength,
            'text_limit' => max(0, $textLimitValue),
        ];
    }

    private function reviewField(string $field, int $postId, $fallback = null)
    {
        if (! function_exists('get_field')) {
            return $fallback;
        }

        $value = get_field($field, $postId);

        return $value !== null && $value !== '' ? $value : $fallback;
    }

    private function reviewImage($image, int $postId): array
    {
        $imageId = 0;
        $url = '';
        $alt = '';

        if (is_array($image)) {
            $imageId = (int) ($image['ID'] ?? $image['id'] ?? 0);
            $url = (string) ($image['sizes']['medium_large'] ?? $image['url'] ?? '');
            $alt = (string) ($image['alt'] ?? '');
        } elseif (is_numeric($image)) {
            $imageId = (int) $image;
        } elseif (is_string($image)) {
            $url = $image;
        }

        if (! $imageId && has_post_thumbnail($postId)) {
            $imageId = (int) get_post_thumbnail_id($postId);
        }

        if (! $url && $imageId) {
            $url = (string) (wp_get_attachment_image_url($imageId, 'medium_large') ?: '');
            $alt = (string) (get_post_meta($imageId, '_wp_attachment_image_alt', true) ?: '');
        }

        return [
            'url' => $url,
            'alt' => $alt,
        ];
    }

    private function reviewTranslate(string $string): string
    {
        return function_exists('pll__') ? pll__($string) : __($string, 'sage');
    }
}
