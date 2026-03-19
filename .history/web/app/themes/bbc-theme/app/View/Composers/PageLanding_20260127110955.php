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
        'cta_text' => get_field('hero_cta_text'),
        'cta_link' => get_field('hero_cta_link'),
        'visual' => get_field('hero_visual')
      ]
    ];
  }
}
