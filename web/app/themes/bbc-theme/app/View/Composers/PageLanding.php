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
        'form' => [
          'headline' => get_field('contact_form_headline'),
          'success' => get_field('contact_form_success'),
          'email_placeholder' => get_field('contact_form_email_placeholder'),
          'message_placeholder' => get_field('contact_form_message_placeholder'),
          'button_text' => get_field('contact_form_button_text'),
        ],
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
    ];
  }
}
